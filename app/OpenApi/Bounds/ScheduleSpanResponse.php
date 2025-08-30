<?php

namespace App\OpenApi\Bounds;

use App\OpenApi\Common\HexbatchUuid;

use App\Models\TimeBoundSpan;
use Carbon\Carbon;
use JsonSerializable;
use OpenApi\Attributes as OA;


/**
 * Show details about a time schedule
 */
#[OA\Schema(schema: 'ScheduleSpanResponse')]
class ScheduleSpanResponse implements  JsonSerializable
{


    #[OA\Property(title: 'Start at',format: 'date-time')]
    public ?string $start = '';

    #[OA\Property(title: 'Stop at',format: 'date-time')]
    public ?string $stop = '';







    public function __construct(TimeBoundSpan $span)
    {

        $this->start = $span->bound_start_ts?
            Carbon::createFromTimestamp($span->bound_start_ts,config('UTC'))->toIso8601String():null;

        $this->stop = $span->bound_stop_ts?
            Carbon::createFromTimestamp($span->bound_stop_ts,config('UTC'))->toIso8601String():null;
    }


    public function jsonSerialize(): array
    {
        $ret = [];
        $ret['start'] = $this->start;
        $ret['stop'] = $this->stop;
        return $ret;
    }

}
