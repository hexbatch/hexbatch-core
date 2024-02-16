<?php


use App\Helpers\Attributes\Apply\StandardAttributes;

return [
    /*
    |--------------------------------------------------------------------------
    | Server Unique Attribute Uuid
    |--------------------------------------------------------------------------
    |
    | This value makes the hbc attributes here unique when compared to other hbc servers.
    | All attributes will inherit from a base attribute with this uuid
    | If you change this you need to start with an empty database again
    |
    */

    'base_attribute_uuid' => env('HBC_SERVER_ATTRIBUTE_UUID', StandardAttributes::getUuid(StandardAttributes::DEFAULT_SERVER_ATTRIBUTE_BASE_UUID)),
];
