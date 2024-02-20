<?php

return [

    /*
    |--------------------------------------------------------------------------
    | application language keys
    |--------------------------------------------------------------------------
    |
    */
    //general
    'this_is_bad_json' => 'The json value is malformed. :issue',
    'cannot_convert_to_json' => 'The value cannot be converted to json :issue',
    'not_map_coordinate' => 'The value is not a map coordinate',
    'not_shape_coordinate' => 'The value is not a shape coordinate',
    'not_timezone' => 'The value is not a timezone',

    //users
    'unique_resource_name_per_user' => 'The :resource_name has already been used.',
    'user_not_found' => 'User not found using :ref',
    'user_not_priv' => 'Not in the admin group of this user',

    //groups
    'group_not_found' => 'Group not found using :ref',
    'group_only_owner_can_delete' => 'Only the group owner can delete a group',
    'group_can_only_be_deleted_if_not_in_use' => 'The group cannot be deleted, its in use somewhere',
    'group_only_owner_can_change_admins' => 'Only the group owner can add or remove admins',
    'group_only_admin_changes_membership' => 'Only an admin of the group can change its membership',
    'group_this_member_does_not_exist' => 'This user :username is not a member in the group',

    //bounds
    'bound_not_found' => 'Bound not found using :ref',
    'bounds_in_use_cannot_change' => 'Bounds cannot edited or deleted because its in use',

    //time bounds
    'time_bound_period_must_be_with_cron' => 'Time bound must have a period defined when a cron string is defined',
    'time_bounds_valid_stop_start' => 'Time bounds must have a valid start and stop, and the start happens before the stop',
    'time_bounds_invalid_cron_string' => 'Time bounds was given an invalid cron string',
    'time_bounds_invalid_time_zone' => 'Time zone is not valid',
    'time_bounds_needs_minimum_info' => 'Time bounds require at least name,start and stop',

    //location bounds
    'location_bound_json_invalid' => 'Location bound was not given valid json',
    'location_out_of_bounds' => 'Location out of bounds',
    'location_wrong_number_coordinates' => 'Location needs two numbers in each point to make a map, and three to make a shape',
    'location_bound_json_invalid_geo_json' => 'Location bound was not given geo json :msg',
    'location_bound_geo_json_not_polygon' => 'Location bound needs polygon or multipolygon geojson',
    'location_bounds_needs_minimum_info' => 'Location bound needs a name and a geo json',
    'location_bounds_only_pings_these' => 'Location bound can only ping with a polygon, a multipology or a point',
    'location_bounds_shape_is_3d' => 'Location bounds shapes must be 3d and all coordinates must have x,y,z',
    'location_bounds_map_is_2d' => 'Location bounds map must be 2d and not cross date line and be in normal numbers, and all coodrinates have lat and long',

    //attributes
    'attribute_not_found' => 'Attribute not found using :ref',
    'attribute_ping_missing_data' => 'Cannot ping, check the ping type and data given for missing inputs',
    'attribute_cannot_be_used_at_parent' => 'This attribute :ref cannot be used as a parent, you do not have permissions',
    'attribute_schema_bounds_violation' => 'When making an attribute, each bounds type has to be in a certain key',
    'attribute_schema_bounds_retired' => 'This bounds, ":bound_name" has been retired and cannot be added to the attribute',
    'attribute_schema_bad_regex' => 'When making an attribute, the value regex needs to be proper for php and not use delimiters. / will be added front and back. :issue',
    'attribute_schema_json_no_primitive' => 'The json value cannot be a primitive',
    'attribute_schema_improper_natural_number' => 'Natural numbers are integers that are >=0',
    'attribute_schema_improper_map_coordinate' => 'Map coodinates must have latitude and longitude properties and numbers in map ranges',
    'attribute_schema_improper_shape_coordinate' => 'Shape coodinates must have properies of x,y,x',
    'attribute_schema_bad_scalar_default' => 'When making an attribute, a scalar default must match the rules given in the default, and must be a scalar value',
    'attribute_schema_unsupported_value' => 'The attribute value type :type is not supported to be cast to a resource',
    'attribute_schema_wrong_value' => 'The attribute value type :type points to the wrong sort of resource :res',
    'attribute_schema_pointers_string_only' => 'The attribute value type :type needs to be identified by a string (uuid or name)',
    'attribute_schema_default_retired' => 'The resource :name is retired and cannot be added as a default value',
    'attribute_schema_bad_meta' => 'Adding a meta requires a minimum of a type and value, language and mime is optional',
    'attribute_schema_unsupported_meta_type' => 'Unsupported attribute meta type of :type',
    'attribute_schema_empty_meta' => 'Meta needs a non empty value',
    'attribute_schema_too_long_meta' => 'Meta needs to be a string and less than 255 characters',
    'attribute_schema_missing_permission_group' => 'When adding user permission groups as object, need the group key to hold the name or id',
    'attribute_schema_need_admin_permission_group' => 'When adding user permission groups, the person needs to be an admin for the group :group_name',
    'attribute_schema_missing_rule_attribute' => 'When adding rules as object, need the attribute key to hold the name or id',
    'attribute_schema_rule_bad_regex' => 'When making an attribute rule, the value regex needs to be proper for php and not use delimiters. / will be added front and back. :issue',
    'attribute_schema_rule_retired' => 'This attribute, ":name" has been retired and new rules using this cannot be added to an attribute',
    'attribute_in_use_cannot_change' => 'Attributes in use cannot edited or deleted. Remove dependencies and try again',

    //remotes
    'remote_not_found' => 'Remote not found using :ref',
    'remote_from_map_invalid_type' => 'Remote from mapping had an invalid type :ref',
    'remote_to_map_invalid_type' => 'Remote to mapping had an invalid type :ref',
    'remote_sensitive_type' => 'Remote method of :method can only be set with permission',
    'remote_invalid_cache_keys' => 'Remote cache keys can only be certain names, found :key',
    'remote_activity_not_found' => 'Remote activity not found using :ref',
    'remote_map_invalid_json_path' => 'The json path is not valid :ref',
    'remote_map_invalid_secret' => 'Secrets can be used on strings but not objects',
    'remote_uncallable' => 'The remote :name exceeded its rate limit and has no cache',


    //actions
    'action_not_found' => 'Element not found using :ref',

    //element types
    'element_type_not_found' => 'Element type not found using :ref',

    //elements
    'element_not_found' => 'Element not found using :ref',



];
