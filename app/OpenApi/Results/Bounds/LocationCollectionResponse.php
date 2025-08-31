<?php

namespace App\OpenApi\Results\Bounds;

use App\Models\LocationBound;
use App\OpenApi\Results\ResultDataBase;
use Illuminate\Pagination\AbstractCursorPaginator;
use OpenApi\Attributes as OA;

/**
 * A collection of time bounds
 */
#[OA\Schema(schema: 'LocationCollectionResponse',title: "Locations")]
class LocationCollectionResponse extends ResultDataBase
{


    #[OA\Property( title: 'List of Schedules')]
    /**
     * @var LocationResponse[] $locations
     */
    public array $locations = [];

    /**
     * @param LocationBound[]|AbstractCursorPaginator $given_types
     */
    public function __construct($given_types)
    {
        parent::__construct($given_types);
        $this->locations = [];
        foreach ($given_types as $loc) {
            $this->locations[] = new LocationResponse(given_location: $loc);
        }

    }

    public  function toArray() : array  {
        $ret = parent::toArray();

        $ret['locations'] = $this->locations;
        return $ret;
    }


}
