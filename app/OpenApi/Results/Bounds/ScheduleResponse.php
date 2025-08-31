<?php

namespace App\OpenApi\Results\Bounds;

use App\Models\TimeBound;
use App\OpenApi\Common\HexbatchUuid;
use App\OpenApi\Results\ResultBase;
use Carbon\Carbon;
use OpenApi\Attributes as OA;


/**
 * Show details about a time schedule
 */
#[OA\Schema(schema: 'ScheduleResponse')]
class ScheduleResponse extends ResultBase
{
    #[OA\Property(title: 'Schedule uuid',type: HexbatchUuid::class)]
    public string $uuid ;

    #[OA\Property(title: 'Name')]
    public string $name = '';


    #[OA\Property(title: 'Start at',format: 'date-time')]
    public ?string $start = '';

    #[OA\Property(title: 'Stop at',format: 'date-time')]
    public ?string $stop = '';

    #[OA\Property(title: 'Period Length')]
    public ?int $period_length = null;

    #[OA\Property(title: 'Cron string')]
    public ?string $cron_string = null;

    #[OA\Property(title: 'Cron string')]
    public ?string $cron_timezone = null;


    #[OA\Property(title: 'Schedule created at',format: 'date-time')]
    public ?string $created_at = '';

    #[OA\Property(title: 'Schedule updated at',format: 'date-time')]
    public ?string $updated_at = '';

    #[OA\Property(title: 'Is schedule on now',format: 'date-time')]
    public bool $is_on_now ;

    #[OA\Property( title: 'Time spans')]
    /**
     * @var ScheduleSpanResponse[] $spans
     */
    public array $spans = [];



    public function __construct(TimeBound $given_time, int $number_spans = 0)
    {
        $this->uuid = $given_time->ref_uuid;
        $this->name = $given_time->getName();

        $this->stop = $given_time->bound_stop?
            Carbon::parse($given_time->bound_stop,'UTC')->timezone(config('app.timezone'))->toIso8601String():null;

        $this->start = $given_time->bound_start?
            Carbon::parse($given_time->bound_start,'UTC')->timezone(config('app.timezone'))->toIso8601String():null;

        $this->created_at = $given_time->created_at?
            Carbon::parse($given_time->created_at,'UTC')->timezone(config('app.timezone'))->toIso8601String():null;

        $this->updated_at = $given_time->updated_at?
            Carbon::parse($given_time->created_at,'UTC')->timezone(config('app.timezone'))->toIso8601String():null;

        $this->period_length = $given_time->bound_period_length;
        $this->cron_string = $given_time->bound_cron;
        $this->cron_timezone = $given_time->bound_cron_timezone;

        if ($number_spans > 0) {
            foreach ($given_time->time_spans as $span) {
                $this->spans[] = new ScheduleSpanResponse($span);
                if (--$number_spans <= 0) {break;}
            }
        }

        $now = $given_time->ping();
        $this->is_on_now = $now?
            Carbon::parse($now,'UTC')->timezone(config('app.timezone'))->toIso8601String():null;

    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['uuid'] = $this->uuid;
        $ret['name'] = $this->name;
        $ret['start'] = $this->start;
        $ret['stop'] = $this->stop;
        $ret['is_on_now'] = $this->is_on_now;
        $ret['created_at'] = $this->created_at;
        $ret['updated_at'] = $this->updated_at;
        $ret['period_length'] = $this->period_length;
        $ret['cron_string'] = $this->cron_string;
        $ret['cron_timezone'] = $this->cron_timezone;
        if (count($this->spans)) {
            $ret['spans'] = $this->spans;
        }


        return $ret;
    }

}
