<?php

namespace App\OpenApi\Results\Bounds;

use App\Models\LocationBound;
use App\OpenApi\Results\ResultThingBase;
use Hexbatch\Things\Models\Thing;
use Hexbatch\Things\OpenApi\Things\ThingMimimalResponseTrait;
use Illuminate\Pagination\AbstractCursorPaginator;
use OpenApi\Attributes as OA;

/**
 * A collection of time bounds
 */
#[OA\Schema(schema: 'LocationCollectionResponse',title: "Locations")]
class LocationCollectionResponse extends ResultThingBase
{
    use ThingMimimalResponseTrait;

    #[OA\Property( title: 'List of Schedules')]
    /**
     * @var LocationResponse[] $locations
     */
    public array $locations = [];

    /**
     * @param LocationBound[]|AbstractCursorPaginator $given_attributes
     */
    public function __construct($given_attributes,?Thing $thing = null)
    {
        parent::__construct($given_attributes,$thing);
        $this->locations = [];
        foreach ($given_attributes as $loc) {
            $this->locations[] = new LocationResponse(given_location: $loc);
        }

    }

    public  function toArray() : array  {
        $ret = parent::toArray();

        $ret['locations'] = $this->locations;
        return $ret;
    }


}
