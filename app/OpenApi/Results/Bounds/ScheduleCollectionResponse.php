<?php

namespace App\OpenApi\Results\Bounds;

use App\Models\TimeBound;
use App\OpenApi\Results\ResultCursorBase;
use Illuminate\Pagination\AbstractCursorPaginator;
use OpenApi\Attributes as OA;

/**
 * A collection of time bounds
 */
#[OA\Schema(schema: 'ScheduleCollectionResponse',title: "Schedules")]
class ScheduleCollectionResponse extends ResultCursorBase
{

    #[OA\Property( title: 'List of Schedules')]
    /**
     * @var ScheduleResponse[] $schedules
     */
    public array $schedules = [];

    /**
     * @param TimeBound[]|AbstractCursorPaginator $given_schedules
     */
    public function __construct($given_schedules, int $number_spans = 3)
    {
        parent::__construct($given_schedules);
        $this->schedules = [];
        foreach ($given_schedules as $schedule) {
            $this->schedules[] = new ScheduleResponse(given_time: $schedule,number_spans: $number_spans);
        }

    }

    public  function toArray() : array  {
        $ret = parent::toArray();

        $ret['schedules'] = $this->schedules;
        return $ret;
    }


}
