<?php

namespace App\OpenApi\ApiResults\Bounds;

use App\Models\LocationBound;
use App\OpenApi\ApiCollectionBase;
use App\OpenApi\ApiResults\ThingResponse;
use App\OpenApi\Results\Bounds\LocationResponse;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use OpenApi\Attributes as OA;


/**
 * Shows the location bound and thing for the api call
 */
#[OA\Schema(schema: 'ApiLocationResponse')]
class ApiLocationResponse extends ApiCollectionBase implements IThingBaseResponse
{

    #[OA\Property(title: 'Location')]
    public LocationResponse $location;

    #[OA\Property(title: 'Thing')]
    public ?ThingResponse $thing = null;


    public function __construct(
        LocationBound $given_location,?Thing $thing = null
    )
    {
        $this->location = new LocationResponse(given_location: $given_location);

        $this->thing = new ThingResponse(thing:$thing);
    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['location'] = $this->location;
        $ret['thing'] = $this->thing;
        return $ret;
    }

}
