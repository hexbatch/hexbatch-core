<?php

return [

    /*
    |--------------------------------------------------------------------------
    | application language keys
    |--------------------------------------------------------------------------
    |
    */

    //users
    'unique_resource_name_per_user' => 'The :resource_name has already been used.',
    'user_not_found' => 'User not found using :ref',
    'user_not_priv' => 'Not in the admin group of this user',

    //groups
    'group_not_found' => 'Group not found using :ref',
    'group_only_owner_can_delete' => 'Only the group owner can delete a group',
    'group_only_owner_can_change_admins' => 'Only the group owner can add or remove admins',
    'group_only_admin_changes_membership' => 'Only an admin of the group can change its membership',
    'group_this_member_does_not_exist' => 'This user :username is not a member in the group',

    //bounds
    'bound_not_found' => 'Time bound not found using :ref',
    'bounds_in_use_cannot_change' => 'Bounds cannot edited or deleted because its in use',

    //time bounds
    'time_bound_period_must_be_with_cron' => 'Time bound must have a period defined when a cron string is defined',
    'time_bounds_valid_stop_start' => 'Time bounds must have a valid start and stop, and the start happens before the stop',
    'time_bounds_invalid_cron_string' => 'Time bounds was given an invalid cron string',
    'time_bounds_invalid_time_zone' => 'Time zone is not valid',
    'time_bounds_needs_minimum_info' => 'Time bounds require at least name,start and stop',

    //location bounds
    'location_bound_json_invalid' => 'Location bound was not given valid json',
    'location_bound_json_invalid_geo_json' => 'Location bound was not given geo json',
    'location_bound_geo_json_not_polygon' => 'Location bound needs polygon or multipolygon geojson',
    'location_bounds_needs_minimum_info' => 'Location bound needs a name and a geo json',
    'location_bounds_only_pings_these' => 'Location bound can only ping with a polygon, a multipology or a point',
];
