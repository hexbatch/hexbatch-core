<?php

namespace App\OpenApi\ApiResults\Bounds;

use App\OpenApi\ApiCollectionBase;
use App\OpenApi\ApiResults\ApiThingResponse;
use App\OpenApi\Results\Bounds\LocationCollectionResponse;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use OpenApi\Attributes as OA;


/**
 * Shows the location bounds list and thing for the api call
 */
#[OA\Schema(schema: 'ApiLocationCollectionResponse')]
class ApiLocationCollectionResponse extends ApiCollectionBase implements IThingBaseResponse
{

    #[OA\Property(title: 'Locations')]
    public LocationCollectionResponse $location_collection;

    #[OA\Property(title: 'Thing')]
    public ?ApiThingResponse $thing = null;


    public function __construct(
        $given_locations,?Thing $thing = null
    )
    {
        $this->location_collection = new LocationCollectionResponse(given_locations: $given_locations);
        $this->thing = new ApiThingResponse(thing:$thing);
    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['location_collection'] = $this->location_collection;
        $ret['thing'] = $this->thing;
        return $ret;
    }

}
