<?php

namespace App\Models;

use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Rules\ResourceNameReq;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property string ref_uuid
 * @property int user_id
 * @property boolean is_retired
 * @property string bound_name
 * @property string bound_start
 * @property string bound_stop
 * @property int bound_period_length
 * @property string bound_cron
 * @property string created_at
 * @property string updated_at
 *
 * @property int bound_start_at_ts
 * @property int bound_stop_at_ts
 * @property int created_at__ts
 * @property int updated_at_ts
 * @property TimeBoundSpan[] time_spans
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
        'is_retired',
        'bound_name',
        'user_id',
        'bound_start',
        'bound_stop',
        'bound_period_length',
        'boundscron'
    ];

    const MAKE_PERIOD_SECONDS = 60*60*6;
    const MAKE_REPEAT_SECONDS = 60*30;

    public function time_spans() : HasMany {
        return $this->hasMany('App\Models\TimeBoundSpan')
            ->orderBy('span_start');
    }

    /**
     * @param int[] $only_ids
     * @throws \Exception
     */
    public static function generateSpans(array $only_ids = []) : void {

        $now_ts = time();
        $unix_timestamp = $now_ts + static::MAKE_PERIOD_SECONDS;
        $query = TimeBound::select('*')
            ->selectRaw(" extract(epoch from  bound_start_at) as bound_start_at_ts,  extract(epoch from  bound_stop_at) as bound_stop_at_ts")
            ->whereRaw('bound_stop > NOW()')
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
        $paginator = $query->cursorPaginate(20, ['*'], 'myCursorId');

        do  {
            /**
             * @var static $item
             */
            foreach ($paginator->items() as $item) {
                $item->makeSpansUntil($unix_timestamp);
            }

            $next = $paginator->nextCursor();
            $paginator = $query->cursorPaginate(20, ['*'], 'myCursorId', $next);
        } while( $paginator->hasPages() );
    }

    /**
     * @throws \Exception
     */
    public function makeSpansUntil(int $unix_timestamp) {
        if ($this->bound_cron) {
            $cron = new \Cron\CronExpression($this->bound_cron);
            $next_time_ts = time();
            $skip = 0;
            while($next_time_ts < $unix_timestamp) {
                $next_date_time = $cron->getNextRunDate('now',$skip++,true);
                $next_time_ts = Carbon::create($next_date_time)->unix();
                $this->insertSpan($next_time_ts,$next_time_ts + ($this->bound_period_length??1 ));
            }
        } else {
            $this->insertSpan($this->bound_start_at_ts,$this->bound_stop_at_ts);
        }
    }

    protected function insertSpan(int $from_ts,int $to_ts) {
        DB::affectingStatement("
                INSERT INTO time_bound_spans(time_bound_id,span_start,span_stop)
                SELECT :bounds_id,:start_at,:stop_at
                WHERE
                NOT EXISTS (
                    SELECT id FROM time_bound_spans
                    WHERE time_bound_id = :bounds_id AND span_start = :start_at and span_stop = :stop_at
                )",
            ['bounds_id'=>$this->id,'start_at'=>$from_ts,'stop_at'=>$to_ts]
        );
    }

    public static function checkCronString(string $cron_string) : bool {
        try {
            $cron = new \Cron\CronExpression($cron_string);
            return true;
        } catch (InvalidArgumentException) {
            return false;
        }
    }
    public static function convertTsToSqlTime(int $unix_ts) : string  {
        return DB::selectOne("SELECT to_timestamp(:unix_ts) as da_time",['unix_ts'=>$unix_ts])->da_time;
    }


    public static function buildTimeBound(?int $user_id = null,?int $id = null) : Builder {
        $build =  TimeBound::select('time_bounds.*')
            ->selectRaw(" extract(epoch from  bound_start_at) as bound_start_at_ts,  extract(epoch from  bound_stop_at) as bound_stop_at_ts")
            /** @uses TimeBound::time_spans() */
            ->with('time_spans');

       if ($user_id) {
           $build->where('user_id',$user_id);
       }

       if ($id) {
           $build->where('id',$user_id);
       }

       return $build;
    }

    /**
     * @param string|null $group_name
     * @return void
     * @throws ValidationException
     */
    public function setBoundName(?string $group_name) {
        Validator::make(['bound_name'=>$group_name], [
            'group_name'=>['required','string','max:128',new ResourceNameReq],
        ])->validate();
        $this->bound_name = $group_name;
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $ret = null;
        try {
            if ($field) {
                $ret = $this->where($field, $value)->first();
            } else {
                if (Utilities::is_uuid($value)) {
                    //the ref
                    $ret = $this->where('ref_uuid', $value)->first();
                } else {
                    if (is_string($value)) {
                        //the name, but scope to the user id of the owner
                        //if this user is not the owner, then the group owner id can be scoped
                        $parts = explode('.', $value);
                        if (count($parts) === 1) {
                            //must be owned by the user
                            $user = auth()->user();
                            $ret = $this->where('user_id', $user?->id)->where('bound_name', $value)->first();
                        } else {
                            $owner = $parts[0];
                            $maybe_name = $parts[1];
                            $owner = (new User)->resolveRouteBinding($owner);
                            $ret = $this->where('user_id', $owner?->id)->where('bound_name', $maybe_name)->first();
                        }


                    }
                }
            }
        } finally {
            if (empty($ret)) {
                throw new HexbatchNotFound(
                    __('msg.time_bound_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::TIME_BOUND_NOT_FOUND
                );
            }
        }
        return $ret;

    }
}
