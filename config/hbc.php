<?php


use App\Helpers\Standard\StandardAttributes;

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
    'system_user_pw' => env('HBC_SYSTEM_USER_PW'),
    'system_secrets_pw' => env('HBC_SYSTEM_SECRETS_PW',''),
];
