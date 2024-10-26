<?php


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



    'version' => env('APP_VERSION', \App\Helpers\Utilities::getVersionString()),

//    todo: put in .env and config the default thing_pagination_size,thing_pagination_limit,thing_depth_limit,
//        thing_rate_limit,thing_backoff_page_policy,thing_backoff_rate_policy thing_json_size_limit


    'system' => [
        'user' => [
            'password' => env('HBC_SYSTEM_USER_PW',''),
            'username' => env('HBC_SYSTEM_USER_NAME',''),
            'uuid' => env('HBC_SYSTEM_USER_UUID',''),
        ],
        'namespace' => [
            'uuid' => env('HBC_SYSTEM_NAMESPACE_UUID',''),
            'public_uuid' => env('HBC_SYSTEM_NAMESPACE_PUBLIC_UUID',''),
            'private_uuid' => env('HBC_SYSTEM_NAMESPACE_PRIVATE_UUID',''),
            'home_uuid' => env('HBC_SYSTEM_NAMESPACE_HOME_UUID',''),
            'handle_uuid' => env('HBC_SYSTEM_NAMESPACE_HANDLE_UUID',''),
            'set_uuid' => env('HBC_SYSTEM_NAMESPACE_SET_UUID',''),
            'name' => env('HBC_SYSTEM_NAMESPACE_NAME',''),
            'public_key' => env('HBC_SYSTEM_NAMESPACE_PUBLIC_KEY',''),
        ],
        'server' => [
            'uuid' => env('HBC_SYSTEM_SERVER_UUID',''),
            'domain' => env('HBC_SYSTEM_SERVER_DOMAIN',''),
            'name' => env('HBC_SYSTEM_SERVER_NAME',''),
        ]
    ]
];
