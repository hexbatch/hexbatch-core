<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;



use App\Data\ApiParams\Data\Locations\Location;
use App\Models\LocationBound;

use App\Sys\Res\Types\Stk\Root\Api;

class ShowLocation extends Api\DesignApi
{
    const UUID = 'fd657291-dee2-4b4e-a971-32c0b08c455a';
    const TYPE_NAME = 'api_design_show_location';


    const PARENT_CLASSES = [
        Api\DesignApi::class
    ];


    public static function showLocation(LocationBound $given_bound) : Location {
        $given_bound->loadMissing('location_namespace','location_attributes','location_attributes.type_owner');
        return Location::validateAndCreate($given_bound);
    }

}

