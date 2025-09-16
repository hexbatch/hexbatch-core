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

    #[OA\Property(title: 'Schedule')]
    public ScheduleCollectionResponse $schedule;

    #[OA\Property(title: 'Thing')]
    public ?ApiThingResponse $thing = null;


    public function __construct(
         $given_times,?Thing $thing = null
    )
    {
        $this->schedule = new ScheduleCollectionResponse(given_schedules: $given_times);

        $this->thing = new ApiThingResponse(thing:$thing);
    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['location'] = $this->schedule;
        $ret['thing'] = $this->thing;
        return $ret;
    }

}
