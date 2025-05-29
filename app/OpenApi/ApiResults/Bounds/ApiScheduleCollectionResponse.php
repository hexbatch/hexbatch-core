<?php

namespace App\OpenApi\ApiResults\Bounds;


use App\OpenApi\ApiCollectionBase;
use App\OpenApi\ApiResults\ApiThingResponse;
use App\OpenApi\Results\Bounds\ScheduleCollectionResponse;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use OpenApi\Attributes as OA;


/**
 * Shows the schedule list and thing for the api call
 */
#[OA\Schema(schema: 'ApiScheduleCollectionResponse')]
class ApiScheduleCollectionResponse extends ApiCollectionBase implements IThingBaseResponse
{

    #[OA\Property(title: 'Schedules')]
    public ScheduleCollectionResponse $schedule_collection;

    #[OA\Property(title: 'Thing')]
    public ?ApiThingResponse $thing = null;


    public function __construct(
         $given_times,?Thing $thing = null
    )
    {
        $this->schedule_collection = new ScheduleCollectionResponse(given_schedules: $given_times);

        $this->thing = new ApiThingResponse(thing:$thing);
    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['schedule_collection'] = $this->schedule_collection;
        $ret['thing'] = $this->thing;
        return $ret;
    }

}
