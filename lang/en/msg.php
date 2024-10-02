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
    'invalid_json_path' => 'The json path is not valid :ref',
    'invalid_enum' => 'Invalid enum: was given :ref but expect :enum_list',

    //users
    'unique_resource_name_per_user' => 'The :resource_name has already been used.',
    'user_not_found' => 'User not found using :ref',
    'user_not_priv' => 'Not in the admin group of this user',

    //user types
    'namespace_not_found' => 'Namespace not found using :ref',
    'namespace_not_owner' => 'You are not the owner of the namespace :ref',
    'namespace_not_admin' => 'You are not an administor of the namespace :ref',
    'namespace_not_member' => 'You are not a member of the namespace :ref',
    'namespace_admin_not_found' => 'The namespace :ref is not an admin of :me',
    'namespace_member_not_found' => 'The namespace :ref is not an admin of :me',
    'namespace_cannot_delete_default' => 'The namespace :ref is the default namespace for :user_name',
    'namespace_cannot_delete_while_in_use' => 'The namespace :ref is still in use, cannot delete',


    //bounds
    'bound_not_found' => 'Bound not found using :ref',

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
    'location_bounds_needs_minimum_info' => 'Location bound needs a name and a geo json and the type of location',
    'location_bounds_has_wrong_type' => 'Location bound needs a valid location type, :bad_type found',
    'location_bounds_only_pings_these' => 'Location bound can only ping with a polygon, a multipology or a point',
    'location_bounds_shape_is_3d' => 'Location bounds shapes must be 3d and all coordinates must have x,y,z',
    'location_bounds_map_is_2d' => 'Location bounds map must be 2d and not cross date line and be in normal numbers, and all coodrinates have lat and long',

    //attributes
    'attribute_not_found' => 'Attribute not found using :ref',
    'attribute_ping_missing_data' => 'Cannot ping, check the ping type and data given for missing inputs',
    'attribute_cannot_be_used_at_parent_final' => 'This attribute :ref cannot be used as a parent, it is either retired or marked to not be used for parenting',
    'attribute_cannot_be_cloned_due_to_pivs' => 'This attribute :ref cannot be cloned by you because of permissions on the source or target. You have to be able to edit both types',
    'attribute_cannot_be_cloned_into_its_type' => 'This attribute :ref cannot be cloned into the same type',
    'attribute_bad_server_access_type' => 'This attribute given a value that is invalid for the attribute_access_type :bad_type',
    'attribute_bad_access_type' => 'This attribute given a value that is invalid for the server_access_type :bad_type',
    'attribute_schema_must_have_name' => 'When making an attribute a name must be provided',
    'attribute_cannot_be_deleted_if_in_use' => 'Can only delete an attribute :ref if nothing uses it',
    'attribute_cannot_be_deleted_priv' => 'Can only delete an attribute :ref in the user admin group or the editing group',
    'attribute_owner_does_not_match_type_given' => 'The type given does not match the attribute :ref type',
    'attribute_unique_name_per_type' => 'Attributes in each type must have unique names',

    'rule_needs_type' => 'Each rule needs a rule_type set. Nothing was set',
    'rule_needs_type_found_bad' => 'Each rule needs a rule_type set. Found :bad_type',
    'rule_can_only_target_attributes_user_can_see' => 'The attribute :ref is not visible at all to the user, so cannot be added to a rule',
    'rule_can_use_group_if_admin' => 'You are not an admin of the  group :ref, so cannot be added to a rule',
    'rule_not_found' => 'The rule was not found by :ref',
    'rule_not_used_by_attribute' => 'The rule :rule is not used by attribute :attr',
    'rule_cannot_be_deleted_if_in_use' => 'The rule :rule cannot be deleted because its attribute :attr in use',
    'rule_requirement_failed' => 'This attribute, ":ref" has forbidden the type from having this combination of attributes in the type',


    //remotes
    'remote_not_found' => 'Remote not found using :ref',
    'remote_not_in_usage_group' => 'You are not allowed to use this remote :ref',
    'remote_schema_meta_map_wrong_type' => 'When adding meta map bounds, this has to be a map type :ref',
    'remote_schema_meta_empty_meta' => 'Meta needs a non empty string value when set: :ref',
    'remote_activity_not_owned_by_your_element' => 'You cannot use this activity because you are not an admin for the element :ref',
    'remote_from_map_invalid_type' => 'Remote from mapping had an invalid type :ref',
    'remote_to_source_invalid_type' => 'Remote to source had an invalid type :ref',
    'remote_to_map_invalid_type' => 'Remote to mapping had an invalid type :ref',
    'remote_sensitive_type' => 'Remote method of :method can only be set with permission',
    'remote_need_uri_type' => 'Remote uri needs to be given a type',
    'remote_mapped_data_type_wrong' => 'The mapped data casting type is wrong :what ',
    'remote_uri_needs_method' => 'Remote uri needs to be given a method',
    'remote_uri_needs_protocol' => 'Remote uri needs to be given a protocol',
    'remote_uri_invalid_from_type' => 'Remote uri only expects the following in the from data type :allowed',
    'remote_uri_invalid_xml_doc' => 'Remote uri needs the xml doc to be a json object',
    'remote_invalid_cache_keys' => 'Remote cache keys can only be certain names, found :key',
    'remote_activity_not_found' => 'Remote activity not found using :ref',
    'remote_activity_only_manual_updated' => 'Can only update manual types of remotes, but not :ref',

    'remote_map_invalid_regex' => 'The regex is not valid :ref',
    'remote_map_invalid_name' => 'The name of the map entry should follow the same rules as the other names and be less than :limit characters :ref : :error',
    'remote_map_invalid_secret' => 'Secrets can be used on strings but not objects',
    'remote_uncallable' => 'The remote :name exceeded its rate limit and has no cache',


    //element types
    'type_not_found' => 'Type not found using :ref',
    'type_must_have_name' => 'Types need a name',
    'type_must_have_map_bound' => 'Types can only use a map bound',
    'child_types_must_be_string_names' => 'The child type must already exist and be refered to by the name, which is a string and not an object',
    'child_type_is_not_inheritable' => 'The child type must allow you to inherit, and also not be retired or final',
    'type_only_owner_can_delete' => 'Only the owner of the type :ref can delete it',
    'type_only_delete_if_unused' => 'Can only delete :ref when it has no elements',

    //elements
    'element_not_found' => 'Element not found using :ref',

    //servers
    'server_not_found' => "Server not found using :ref"



];
