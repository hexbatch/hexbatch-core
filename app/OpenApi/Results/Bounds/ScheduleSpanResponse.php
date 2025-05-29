<?php

namespace App\OpenApi\Results\Bounds;

use App\Models\TimeBoundSpan;
use App\OpenApi\Results\ResultBase;
use Carbon\Carbon;
use OpenApi\Attributes as OA;


/**
 * Show details about a time schedule
 */
#[OA\Schema(schema: 'ScheduleSpanResponse')]
class ScheduleSpanResponse extends ResultBase
{

    #[OA\Property(title: 'Start at',format: 'date-time')]
    public ?string $start = '';

    #[OA\Property(title: 'Stop at',format: 'date-time')]
    public ?string $stop = '';







    public function __construct(TimeBoundSpan $span)
    {
        parent::__construct();
        $this->start = $span->bound_start_ts?
            Carbon::createFromTimestamp($span->bound_start_ts,config('UTC'))->toIso8601String():null;

        $this->stop = $span->bound_stop_ts?
            Carbon::createFromTimestamp($span->bound_stop_ts,config('UTC'))->toIso8601String():null;
    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['start'] = $this->start;
        $ret['stop'] = $this->stop;
        return $ret;
    }

}
