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
    'group_not_admin' => 'This user :username is not an admin in the group :group',
    'group_new_has_no_name' => 'Cannot make a new group without a name',

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
    'location_bounds_needs_minimum_info' => 'Location bound needs a name and a geo json and the type of location',
    'location_bounds_has_wrong_type' => 'Location bound needs a valid location type, :bad_type found',
    'location_bounds_only_pings_these' => 'Location bound can only ping with a polygon, a multipology or a point',
    'location_bounds_shape_is_3d' => 'Location bounds shapes must be 3d and all coordinates must have x,y,z',
    'location_bounds_map_is_2d' => 'Location bounds map must be 2d and not cross date line and be in normal numbers, and all coodrinates have lat and long',

    //attributes
    'attribute_not_found' => 'Attribute not found using :ref',
    'attribute_ping_missing_data' => 'Cannot ping, check the ping type and data given for missing inputs',
    'attribute_cannot_be_used_at_parent_permissions' => 'This attribute :ref cannot be used as a parent, you do not have permissions',
    'attribute_cannot_be_used_at_parent_final' => 'This attribute :ref cannot be used as a parent, it is either retired or marked to not be used for parenting',
    'attribute_parent_cannnot_change' => 'This attribute :ref cannot change its parent after creation',
    'attribute_cannot_be_edited_due_to_pivs' => 'This attribute :ref cannot be edited by you because you are neither the owner, in the owner admin group, or in the edit admin group',
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


    'attribute_rule_missing_attribute' => 'When adding rules as object, need the attribute key to hold the name or id',
    'attribute_rule_bad_regex' => 'When making an attribute rule, the value regex needs to be proper for php and not use delimiters. / will be added front and back. :issue',
    'attribute_rule_retired' => 'This attribute, ":name" has been retired and new rules using this cannot be added to an attribute',


    //remotes
    'remote_not_found' => 'Remote not found using :ref',
    'remote_not_in_usage_group' => 'You are not allowed to use this remote :ref',
    'remote_schema_meta_map_wrong_type' => 'When adding meta map bounds, this has to be a map type :ref',
    'remote_schema_meta_empty_meta' => 'Meta needs a non empty string value when set: :ref',
    'remote_schema_missing_permission_group' => 'When adding user permission groups as object, need the group key to hold the name or id',
    'remote_schema_need_admin_permission_group' => 'Only group admins can add that group to be a remote usage group :group_name',
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
    'stack_not_found' => 'Remote stack not found using :ref',

    //stack
    'stack_not_in_usage_group' => 'You are not allowed to use this stack :ref',

    //actions
    'action_not_found' => 'Element not found using :ref',

    //element types
    'element_type_not_found' => 'Element type not found using :ref',
    'element_type_must_have_name' => 'Element types need a name',
    'element_type_not_admin' => 'You are not in the owner admin group or a member in the editing group for the type :ref',
    'element_type_not_viewer' => 'You are not in the owner admin group or a member in the editing or inheriting group for the type :ref',
    'element_type_only_owner_can_delete' => 'Only the owner of the type :ref can delete it',
    'element_type_only_delete_if_unused' => 'Can only delete :ref when it has no elements',

    //elements
    'element_not_found' => 'Element not found using :ref',

    //servers
    'server_not_found' => "Server not found using :ref"



];
