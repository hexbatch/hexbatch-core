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

    //groups
    'group_not_found' => 'Group not found using :ref',
    'group_only_owner_can_delete' => 'Only the group owner can delete a group',
    'group_only_owner_can_change_admins' => 'Only the group owner can add or remove admins',
    'group_only_admin_changes_membership' => 'Only an admin of the group can change its membership',
    'group_this_member_does_not_exist' => 'This user :username is not a member in the group',
];
