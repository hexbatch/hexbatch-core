<?php

namespace App\OpenApi\Results\Bounds;

use App\Models\LocationBound;
use App\OpenApi\Results\ResultCursorBase;
use Illuminate\Pagination\AbstractCursorPaginator;
use OpenApi\Attributes as OA;

/**
 * A collection of time bounds
 */
#[OA\Schema(schema: 'LocationCollectionResponse',title: "Locations")]
class LocationCollectionResponse extends ResultCursorBase
{

    #[OA\Property( title: 'List of Schedules')]
    /**
     * @var LocationResponse[] $locations
     */
    public array $locations = [];

    /**
     * @param LocationBound[]|AbstractCursorPaginator $given_locations
     */
    public function __construct($given_locations)
    {
        parent::__construct($given_locations);
        $this->locations = [];
        foreach ($given_locations as $loc) {
            $this->locations[] = new LocationResponse(given_location: $loc);
        }

    }

    public  function toArray() : array  {
        $ret = parent::toArray();

        $ret['locations'] = $this->locations;
        return $ret;
    }


}
