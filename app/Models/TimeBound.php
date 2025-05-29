<?php

namespace App\Models;

use App\Enums\Types\TypeOfLifecycle;
use App\Exceptions\HexbatchCoreException;
use App\Exceptions\HexbatchNameConflictException;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Rules\BoundNameReq;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Carbon\Exceptions\InvalidTimeZoneException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int time_bound_namespace_id
 * @property string ref_uuid

 * @property string bound_name
 * @property string bound_start
 * @property string bound_stop
 * @property int bound_period_length
 * @property string bound_cron
 * @property string bound_cron_timezone
 * @property string created_at
 * @property string updated_at
 *
 * @property int bound_start_ts
 * @property int bound_stop_ts
 * @property int created_at_ts
 * @property int updated_at_ts
 * @property TimeBoundSpan[] time_spans
 * @property AttributeRule[] time_attributes
 *
 */
class TimeBound extends Model
{

    protected $table = 'time_bounds';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bound_name',
        'bound_start',
        'bound_stop',
        'bound_period_length',
        'boundscron'
    ];

    const MAKE_PERIOD_SECONDS = 60*60*6;
    const MAKE_REPEAT_SECONDS = 60*30;


    public function time_attributes() : HasMany {
        return $this->hasMany(Attribute::class,'attribute_time_bound_id','id')
            ->orderBy('span_start');
    }

    public function time_spans() : HasMany {
        return $this->hasMany(TimeBoundSpan::class)
            ->select('*')
            ->selectRaw(" extract(epoch from lower(time_slice_range)) as bound_start_ts, extract(epoch from upper(time_slice_range)) as bound_stop_ts")
            ->orderBy('span_start');
    }

    public function getName() {
        return $this->bound_name;
    }



    /**
     * @param int[] $only_ids

     */
    public static function generateSpans(array $only_ids = []) : void {

        $now_ts = time();
        $unix_timestamp = $now_ts + static::MAKE_PERIOD_SECONDS;
        $query = TimeBound::select('*')
            ->selectRaw(" extract(epoch from lower(time_slice_range)) as bound_start_ts, extract(epoch from upper(time_slice_range)) as bound_stop_ts")
            ->whereRaw('upper(time_slice_range) >= NOW()')
            ->join('time_bound_spans',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($now_ts) {
                    $join
                        ->on('time_bounds.id','=','time_bound_spans.time_bound_id')
                        ->where('time_bound_spans.span_start','>',$now_ts + TimeBound::MAKE_REPEAT_SECONDS);
                }
            )
            ->whereNull('time_bound_spans.id')
            ->where('time_bounds.bound_stop','>',$now_ts)
            ->orderBy('time_bounds.id');

        if (count($only_ids)) {
            $query->whereIn('id',$only_ids);
        }
        $paginator = $query->cursorPaginate(20, ['*'], 'timeSpanCursorId');

        do  {
            /**
             * @var static $item
             */
            foreach ($paginator->items() as $item) {
                $item->makeSpansUntil($unix_timestamp);
            }

            $next = $paginator->nextCursor();
            $paginator = $query->cursorPaginate(20, ['*'], 'timeSpanCursorId', $next);
        } while( $paginator->hasPages() );
    }


    public function makeSpansUntil(int $unix_timestamp) {
        try {
            if ($this->bound_cron) {
                $cron = new \Cron\CronExpression($this->bound_cron);
                $next_time_ts = time();
                $skip = 0;
                while ($next_time_ts < $unix_timestamp) {
                    $next_date_time = $cron->getNextRunDate('now', $skip++, true, $this->bound_cron_timezone);
                    $next_time_ts = Carbon::create($next_date_time)->unix();
                    $this->insertSpan($next_time_ts, $next_time_ts + ($this->bound_period_length ?? 1));
                }
            } else {
                $this->insertSpan($this->bound_start_ts, $this->bound_stop_ts);
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(),$e->getCode(),$e);
        }
    }

    protected function insertSpan(int $from_ts,int $to_ts) {

        DB::affectingStatement("
                INSERT INTO time_bound_spans(time_bound_id,time_slice_range)
                SELECT :bounds_id,tstzrange( to_timestamp(:start_at),to_timestamp(:stop_at) )
                WHERE
                NOT EXISTS (
                    SELECT id FROM time_bound_spans
                    WHERE time_bound_id = :bounds_id AND tstzrange( to_timestamp(:start_at),to_timestamp(:stop_at) )  <@ time_slice_range
                )",
            ['bounds_id'=>$this->id,'start_at'=>$from_ts,'stop_at'=>$to_ts]
        );
    }

    public static function convertTsToSqlTime(int $unix_ts) : string  {
        return DB::selectOne("SELECT to_timestamp(:unix_ts) as da_time",['unix_ts'=>$unix_ts])->da_time;
    }


    public static function buildTimeBound(?int $id = null,?int $type_id = null,?int $attribute_id = null) : Builder {
        $build =  TimeBound::select('time_bounds.*')
            ->selectRaw(" extract(epoch from  time_bounds.bound_start) as bound_start_ts,  extract(epoch from  time_bounds.bound_stop) as bound_stop_ts")
            /** @uses TimeBound::time_spans(),TimeBound::time_attributes() */
            ->with('time_spans','time_attributes');



        if ($attribute_id) {
            $build->join('attributes as bounded_attr',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join
                        ->on('time_bounds.id','=','bounded_attr.attribute_time_bound_id');
                }
            );
        }

        if ($type_id) {

            $build->join('element_types as bounded_type',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join
                        ->on('time_bounds.id','=','bounded_type.type_time_bound_id');
                }
            );


        }

       if ($id) {
           $build->where('id',$id);
       }

       return $build;
    }



    protected function setPeriodLength(int $p) {
        if ($p < 1) {
            throw new HexbatchCoreException(__("msg.time_bound_period_must_be_with_cron"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BOUND_INVALID_PERIOD);
        }
        $this->bound_period_length = $p;
    }
    protected function setCronString(string $cron_string) {
        try {
            new \Cron\CronExpression($cron_string);
        } catch (InvalidArgumentException $er_c) {
            throw new HexbatchNameConflictException(__("msg.time_bounds_invalid_cron_string"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BOUND_INVALID_CRON,$er_c);
        }
        $this->bound_cron = $cron_string;
    }

    protected function setTimezone(?string $timezone) {
        if (empty(trim($timezone))) {$this->bound_cron_timezone = null; return;}

        try {
            Carbon::now()->timezone($timezone);
        } catch (InvalidTimeZoneException $er_c) {
            throw new HexbatchNameConflictException(__("msg.time_bounds_invalid_time_zone"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BOUND_INVALID_CRON,$er_c);
        }
        $this->bound_cron_timezone = $timezone;
    }


    public function setTimes(string|int $start, string|int $stop,
                             ?string $bound_cron = null, ?int $period_length = null,?string $bound_cron_timezone = null
    ) :void
    {
        if (empty($start) || empty($stop)) {
            throw new HexbatchNameConflictException(__("msg.time_bounds_valid_stop_start"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BOUND_INVALID_START_STOP);
        }

        try {
            $bound_start_ts = Carbon::create($start)->unix();
            $bound_stop_ts = Carbon::create($stop)->unix();
        } catch (InvalidFormatException $ie) {
            throw new HexbatchNameConflictException(__("msg.time_bounds_valid_stop_start"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BOUND_INVALID_START_STOP,$ie);
        }

        if ($bound_stop_ts < $bound_start_ts) {
            throw new HexbatchNameConflictException(__("msg.time_bounds_valid_stop_start"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BOUND_INVALID_START_STOP);
        }
        $this->bound_start = TimeBound::convertTsToSqlTime($bound_start_ts);
        $this->bound_stop = TimeBound::convertTsToSqlTime($bound_stop_ts);

        $this->bound_cron = $bound_cron;
        if ($bound_cron) {
            $this->setCronString($bound_cron) ;
            $this->setPeriodLength($period_length);
        }
        $this->setTimezone($bound_cron_timezone);
        $this->save();
        $this->redoTimeSpans();
    }


    public function redoTimeSpans() {
        TimeBoundSpan::where('time_bound_id',$this->id)->delete();
        $this->makeSpansUntil(time() + TimeBound::MAKE_PERIOD_SECONDS);
    }

    public function ping(?string $time_to_ping = null) : ?int {
        if ($time_to_ping) {
            $ping_ts = Carbon::create($time_to_ping)->unix();
        } else {
            $ping_ts = Carbon::now()->unix();
        }
        $hit = TimeBoundSpan::where('time_bound_id',$this->id)->where('span_start','<=',$ping_ts)->where('span_stop','>=',$ping_ts)->first();
        if ($hit) {return $ping_ts;}
        return null;
    }


    public function resolveRouteBinding($value, $field = null)
    {
        $build = null;
        $ret = null;
        try {
            if ($field) {
                $ret = $this->where($field, $value)->first();
            } else {
                if (Utilities::is_uuid($value)) {
                    $build = $this->where('ref_uuid', $value);
                } else {
                    if (is_string($value)) {
                        $parts = explode(UserNamespace::NAMESPACE_SEPERATOR, $value);
                        if (count($parts) === 2) {
                            $owner_hint = $parts[0];
                            $maybe_name = $parts[1];
                            /**
                             * @var UserNamespace $owner
                             */
                            $owner = (new UserNamespace())->resolveRouteBinding($owner_hint);
                            $build = $this->where('time_bound_namespace_id', $owner?->id)->where('bound_name', $maybe_name);
                        }

                        if (count($parts) === 3) {
                            $server_hint = $parts[0];
                            $namespace_hint = $parts[1];
                            $maybe_name = $parts[2];
                            /**
                             * @var UserNamespace $owner
                             */
                            $owner = (new UserNamespace())->resolveRouteBinding($server_hint.UserNamespace::NAMESPACE_SEPERATOR.$namespace_hint);
                            $build = $this->where('time_bound_namespace_id', $owner?->id)->where('bound_name', $maybe_name);
                        }
                    }
                }
            }

            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {
                    $first_build = TimeBound::buildTimeBound(id: $first_id);
                    $ret = $first_build->first();
                }
            }
        }
        catch (\Exception $e) {
            Log::warning('TimeBound resolving: '. $e->getMessage());
        }
        finally {
            if (empty($ret)) {
                throw new HexbatchNotFound(
                    __('msg.bound_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::BOUND_NOT_FOUND
                );
            }
        }
        return $ret;
    }

    /**
     * @throws \Exception
     */
    public static function collectTimeBound(Collection|string $collect, UserNamespace $namespace) : TimeBound {
        try {
            DB::beginTransaction();
            if (is_string($collect) && Utilities::is_uuid($collect)) {
                $bound = (new TimeBound())->resolveRouteBinding($collect);
            } else {
                $bound = new TimeBound();
                $bound->time_bound_namespace_id = $namespace->id;
                if ($collect->has('bound_name')) {
                    $name = $collect->get('bound_name');
                    if (is_string($name) && Str::trim($name)) {
                        try {
                            Validator::make(['time_bound_name' => $name], [
                                'time_bound_name' => ['required', 'string', new BoundNameReq()],
                            ])->validate();
                        } catch (ValidationException $v) {
                            throw new HexbatchNotPossibleException($v->getMessage(),
                                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                                RefCodes::BOUND_INVALID_NAME);
                        }
                        $bound->bound_name = Str::trim($name);
                    }
                }
                if (!$bound->isInUse()) {
                    if ($collect->has('bound_start')) {
                        $test = $collect->get('bound_start');
                        if (is_string($test) && Str::trim($test)) {
                            $bound->bound_start = Str::trim($test);
                        }
                    }

                    if ($collect->has('bound_stop')) {
                        $test = $collect->get('bound_stop');
                        if (is_string($test) && Str::trim($test)) {
                            $bound->bound_stop = Str::trim($test);
                        }
                    }

                    if ($collect->has('bound_cron')) {
                        $test = $collect->get('bound_cron');
                        if (is_string($test) && Str::trim($test)) {
                            $bound->bound_cron = Str::trim($test);
                        }
                    }

                    if ($collect->has('bound_cron_timezone')) {
                        $test = $collect->get('bound_cron_timezone');
                        if (is_string($test) && Str::trim($test)) {
                            $bound->bound_cron_timezone = Str::trim($test);
                        }
                    }

                    if ($collect->has('bound_period_length')) {
                        $test = $collect->get('bound_period_length');
                        if (intval($test) && intval($test) > 0) {
                            $bound->bound_period_length = intval($test);
                        }
                    }

                    if (!$bound->bound_name || !$bound->bound_stop || !$bound->bound_start) {
                        throw new HexbatchCoreException(__("msg.time_bounds_needs_minimum_info"),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::BOUND_NEEDS_MIN_INFO);
                    }

                    //this saves too
                    $bound->setTimes(start: $bound->bound_start, stop: $bound->bound_stop,
                        bound_cron: $bound->bound_cron, period_length: $bound->bound_period_length,
                        bound_cron_timezone: $bound->bound_cron_timezone); //saves and processes
                }

                $bound->refresh();

                $bound = TimeBound::buildTimeBound(id: $bound->id)->first();

            }//end else



            DB::commit();
            return $bound;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function isInUse() : bool {
        if (!$this->id) {return false;}
        return ElementType::buildElementType(time_bound_id: $this->id)
            ->where('lifecycle','<>',TypeOfLifecycle::DEVELOPING)->exists();
    }

}
