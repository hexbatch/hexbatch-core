<?php

namespace App\Models;

use App\Exceptions\HexbatchCoreException;
use App\Exceptions\HexbatchNameConflictException;
use App\Exceptions\RefCodes;
use App\Models\Traits\TBoundsCommon;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Carbon\Exceptions\InvalidTimeZoneException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
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
 * @property string bound_cron_timezone
 * @property string created_at
 * @property string updated_at
 *
 * @property int bound_start_ts
 * @property int bound_stop_ts
 * @property int created_at_ts
 * @property int updated_at_ts
 * @property TimeBoundSpan[] time_spans
 * @property User bound_owner
 *
 */
class TimeBound extends Model
{
    use TBoundsCommon;

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


    public function bound_owner() : BelongsTo {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function time_spans() : HasMany {
        return $this->hasMany('App\Models\TimeBoundSpan')
            ->orderBy('span_start');
    }

    public function isInUse() : bool {
        return false;
    }

    /**
     * @param int[] $only_ids
     * @throws \Exception
     */
    public static function generateSpans(array $only_ids = []) : void {

        $now_ts = time();
        $unix_timestamp = $now_ts + static::MAKE_PERIOD_SECONDS;
        $query = TimeBound::select('*')
            ->selectRaw(" extract(epoch from  bound_start) as bound_start_ts,  extract(epoch from  bound_stop) as bound_stop_ts")
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
                $next_date_time = $cron->getNextRunDate('now',$skip++,true,$this->bound_cron_timezone);
                $next_time_ts = Carbon::create($next_date_time)->unix();
                $this->insertSpan($next_time_ts,$next_time_ts + ($this->bound_period_length??1 ));
            }
        } else {
            $this->insertSpan($this->bound_start_ts,$this->bound_stop_ts);
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

    public static function convertTsToSqlTime(int $unix_ts) : string  {
        return DB::selectOne("SELECT to_timestamp(:unix_ts) as da_time",['unix_ts'=>$unix_ts])->da_time;
    }


    public static function buildTimeBound(?int $user_id = null,?int $id = null) : Builder {
        $build =  TimeBound::select('time_bounds.*')
            ->selectRaw(" extract(epoch from  bound_start) as bound_start_ts,  extract(epoch from  bound_stop) as bound_stop_ts")
            /** @uses TimeBound::time_spans() */
            ->with('time_spans');

       if ($user_id) {
           $build->where('user_id',$user_id);
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

    /**
     * @throws \Exception
     */
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

    /**
     * @throws \Exception
     */
    public function redoTimeSpans() {
        TimeBoundSpan::where('time_bound_id',$this->id)->delete();
        $this->makeSpansUntil(time() + TimeBound::MAKE_PERIOD_SECONDS);
    }


}
