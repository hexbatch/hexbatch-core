<?php

namespace App\OpenApi\Results\Bounds;

use App\Models\TimeBound;
use App\OpenApi\Results\ResultDataBase;
use Illuminate\Pagination\AbstractCursorPaginator;
use OpenApi\Attributes as OA;

/**
 * A collection of time bounds
 */
#[OA\Schema(schema: 'ScheduleCollectionResponse',title: "Schedules")]
class ScheduleCollectionResponse extends ResultDataBase
{


    #[OA\Property( title: 'List of Schedules')]
    /**
     * @var ScheduleResponse[] $schedules
     */
    public array $schedules = [];

    /**
     * @param TimeBound[]|AbstractCursorPaginator $given_attributes
     */
    public function __construct($given_attributes, int $number_spans = 3)
    {
        parent::__construct($given_attributes);
        $this->schedules = [];
        foreach ($given_attributes as $schedule) {
            $this->schedules[] = new ScheduleResponse(given_time: $schedule,number_spans: $number_spans);
        }

    }

    public  function toArray() : array  {
        $ret = parent::toArray();

        $ret['schedules'] = $this->schedules;
        return $ret;
    }


}
