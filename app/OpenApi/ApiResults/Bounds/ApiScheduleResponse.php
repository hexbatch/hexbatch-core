<?php

namespace App\OpenApi\ApiResults\Bounds;

use App\Models\TimeBound;
use App\OpenApi\ApiCollectionBase;
use App\OpenApi\ApiResults\ThingResponse;
use App\OpenApi\Results\Bounds\ScheduleResponse;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use OpenApi\Attributes as OA;


/**
 * Shows the time bound and thing for the api call
 */
#[OA\Schema(schema: 'ApiScheduleResponse')]
class ApiScheduleResponse extends ApiCollectionBase implements IThingBaseResponse
{

    #[OA\Property(title: 'Schedule')]
    public ScheduleResponse $schedule;

    #[OA\Property(title: 'Thing')]
    public ?ThingResponse $thing = null;


    public function __construct(
        TimeBound $given_time,?Thing $thing = null
    )
    {
        $this->schedule = new ScheduleResponse(given_time: $given_time);

        $this->thing = new ThingResponse(thing:$thing);
    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['location'] = $this->schedule;
        $ret['thing'] = $this->thing;
        return $ret;
    }

}
