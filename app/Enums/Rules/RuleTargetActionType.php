<?php

namespace App\Enums\Rules;
/**
 * postgres enum rule_target_action_type
 */
enum RuleTargetActionType: string
{

    case NO_ACTION = 'no_action';
    case PRAGMA_FACET_OFFSET = 'pragma_facet_offset';
    case PRAGMA_FACET_ROTATION = 'pragma_facet_rotation';
    case PRAGMA_ELEMENT_ON = 'pragma_element_on';
    case PRAGMA_ELEMENT_TOGGLE = 'pragma_element_toggle';
    case PRAGMA_ELEMENT_OFF = 'pragma_element_off';
    case PRAGMA_ELEMENT_TYPE_ON = 'pragma_element_type_on';
    case PRAGMA_ELEMENT_TYPE_TOGGLE = 'pragma_element_type_toggle';
    case PRAGMA_ELEMENT_TYPE_OFF = 'pragma_element_type_off';
    case COMMAND_MAKE_SET = 'command_make_set';
    case COMMAND_DESTROY_SET = 'command_destroy_set';
    case COMMAND_ADD_TO_SET = 'command_add_to_set';
    case COMMAND_CHANGE_SET = 'command_change_set';
    case COMMAND_DESTROY_USER = 'command_destroy_user';
    case COMMAND_ASSIGN_USER_TO_NAMESPACE = 'command_assign_user_to_namespace';
    case COMMAND_CREATE_ELEMENT = 'command_create_element';
    case COMMAND_DESTROY_ELEMENT = 'command_destroy_element';
    case COMMAND_NAMESPACE_ADD_MEMBER = 'command_namespace_add_member';
    case COMMAND_NAMESPACE_ADD_ADMIN = 'command_namespace_add_admin';
    case COMMAND_NAMESPACE_REMOVE_MEMBER = 'command_namespace_remove_member';
    case COMMAND_NAMESPACE_REMOVE_ADMIN = 'command_namespace_remove_admin';
    case TYPE_ATTRIBUTE_REQUIRED = 'type_attribute_required';
    case SET_MEMBERSHIP_AFFINITY = 'set_membership_affinity';
    case WRITE = 'write';
    case READ = 'read';

}


