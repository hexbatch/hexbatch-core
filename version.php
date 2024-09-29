<?php

//todo put version here, and not in the .env
$test = [
    'nothing',

    'attribute_read',
    'attribute_write',
    'attribute_pragma',
    'attribute_turned_off',
    'attribute_urned_on',


    'element_creation',
    'element_batch_creation',
    'element_destruction',

    'group_operation',


            'remote',
            'stack',

            'search_results',


            'set_operation',
            'set_enter',
            'set_leave',
            'set_contents_shape_changed',
            'set_transport',
            'set_kick',
            'set_child_created',
            'set_child_destroyed',
            'set_top_level_destroyed',
            'set_link_created',
            'set_link_destroyed',


            'server_add_element',
            'server_add_type',
            'server_add_set',
            'server_remove_element',
            'server_remove_type',
            'server_remove_set',
            'server_run_rules',
            'server_read',
            'server_write',
            'server_get_user_token',
            'server_add_remote_user',
            'server_user_regenerate_key',
            'server_remove_remote_user',
            'server_created',
            'server_allowed',
            'server_removed',
            'server_after_removed',
            'server_paused',
            'server_regenerate_key',

            'server_sent_callback_server_regenerated_key',
            'server_sent_callback_user_regenerated_key',
            'server_sent_callback_got_user_token',
            'server_sent_callback_removed_remote_user',
            'server_sent_callback_create',
            'server_sent_callback_destroy',
            'server_sent_callback_run_rules',
            'server_sent_callback_read',
            'server_sent_callback_write',
            'server_sent_callback_element_add',
            'server_sent_callback_element_request',
            'server_sent_callback_element_remove',
            'server_sent_callback_set_add',
            'server_sent_callback_ask_user_permission',


             'shape_intersection_enter',
             'shape_intersection_leave',
             'shape_bordering_attached',
             'shape_bordering_seperated',


             'type_parent_add',
            'type_attribute_parent_add',
            'type_parent_add',
            'type_created_before',
            'type_updated_before',
            'type_created_after',
            'type_created_after',


             'user_add_before',
             'user_remove_before',
             'user_add_after',
             'user_remove_after',
             'user_owner_change',


             'user_group_member_add',
             'user_group_admin_add',
             'user_admin_removing_member',
             'user_owner_removing_admin'
];

return [
  'version' => '0.2.0'
];
