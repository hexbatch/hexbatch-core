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



    'version' => env('APP_VERSION', \Hexbatch\Things\Helpers\ThingUtilities::getVersionAsString(for_lib: false)),

    'pagination' => [
       'default_page_size' => (int)env('DEFAULT_CURSOR_PAGE_SIZE',15),
       'default_element_limit' => (int)env('DEFAULT_ELEMENT_LIMIT',\App\Models\Element::DEFAULT_ELEMENT_LIMIT)
    ],

    'system' => [
        'user' => [
            'password' => env('HBC_SYSTEM_USER_PW',''),
            'username' => env('HBC_SYSTEM_USER_NAME',''),
            'uuid' => env('HBC_SYSTEM_USER_UUID',''),
        ],
        'namespace' => [
            'uuid' => env('HBC_SYSTEM_NAMESPACE_UUID',''),
            'name' => env('HBC_SYSTEM_NAMESPACE_NAME',''),
            'public_key' => env('HBC_SYSTEM_NAMESPACE_PUBLIC_KEY',''),
            'base_type_uuid' => env('HBC_SYSTEM_NAMESPACE_TYPE_UUID',''),
            'base_type_name' => env('HBC_SYSTEM_NAMESPACE_TYPE_NAME','system_namespace'),

            'elements_and_sets' => [
                'public_uuid' => env('HBC_SYSTEM_NAMESPACE_PUBLIC_UUID',''),
                'private_uuid' => env('HBC_SYSTEM_NAMESPACE_PRIVATE_UUID',''),
                'home_uuid' => env('HBC_SYSTEM_NAMESPACE_HOME_UUID',''),
                'handle_uuid' => env('HBC_SYSTEM_NAMESPACE_HANDLE_UUID',''),
                'set_uuid' => env('HBC_SYSTEM_NAMESPACE_SET_UUID',''),
            ],


            'types' => [
                'ns_uuid' => env('',''),
                'public_type_uuid' => env('HBC_SYSTEM_NAMESPACE_PUBLIC_TYPE_UUID',''),
                'private_type_uuid' => env('HBC_SYSTEM_NAMESPACE_PRIVATE_TYPE_UUID',''),
                'homeset_type_uuid' => env('HBC_SYSTEM_NAMESPACE_HOME_SET_TYPE_UUID',''),
                'handle_type_uuid' => env('HBC_SYSTEM_NAMESPACE_HANDLE_TYPE_UUID',''),
            ]
        ],
        'server' => [
            'type_uuid' => env('HBC_SYSTEM_SERVER_TYPE_UUID',''),
            'uuid' => env('HBC_SYSTEM_SERVER_UUID',''),
            'domain' => env('HBC_SYSTEM_SERVER_DOMAIN',''),
            'url' => env('HBC_SYSTEM_SERVER_URL',''),
            'name' => env('HBC_SYSTEM_SERVER_NAME',''),
        ],
        'loggging' => [
            'timezone' => env('HBC_LOGGING_TIMEZONE','UTC'),
        ]
    ]
];
