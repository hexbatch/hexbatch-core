<?php

namespace App\Enums\Rules;
/**
 * postgres enum rule_target_action_type
 */
enum RuleTargetActionType: string
{

    case NO_ACTION = 'no_action';
    case PRAGMA_SHAPE_OFFSET = 'pragma_shape_offset';
    case PRAGMA_SHAPE_ROTATION = 'pragma_shape_rotation';
    case pragma_shape_color = 'pragma_shape_color';
    case pragma_shape_texture = 'pragma_shape_texture';
    case pragma_shape_opacity = 'pragma_shape_opacity';
    case pragma_shape_zorder = 'pragma_shape_zorder';

    case PRAGMA_ELEMENT_ON = 'pragma_element_on';
    case PRAGMA_ELEMENT_TOGGLE = 'pragma_element_toggle';
    case PRAGMA_ELEMENT_OFF = 'pragma_element_off';
    case PRAGMA_ELEMENT_TYPE_ON = 'pragma_element_type_on';
    case PRAGMA_ELEMENT_TYPE_TOGGLE = 'pragma_element_type_toggle';
    case PRAGMA_ELEMENT_TYPE_OFF = 'pragma_element_type_off';
    case COMMAND_RUN_REMOTE = 'command_run_remote';
    case COMMAND_MAKE_SET = 'command_make_set';
    case COMMAND_DESTROY_SET = 'command_destroy_set';
    case COMMAND_ADD_TO_SET = 'command_add_to_set';
    case COMMAND_CHANGE_SET = 'command_change_set';
    case COMMAND_DESTROY_USER = 'command_destroy_user';
    case COMMAND_ASSIGN_USER_TO_NAMESPACE = 'command_assign_user_to_namespace';
    case COMMAND_CREATE_ELEMENT = 'command_create_element';
    case COMMAND_DESTROY_ELEMENT = 'command_destroy_element';
    case COMMAND_ADD_LIVE_TYPE_ELEMENT = 'command_add_live_type_element';
    case COMMAND_REMOVE_LIVE_TYPE_ELEMENT = 'command_remove_live_type_element';
    case COMMAND_NAMESPACE_ADD_MEMBER = 'command_namespace_add_member';
    case COMMAND_NAMESPACE_ADD_ADMIN = 'command_namespace_add_admin';
    case COMMAND_NAMESPACE_REMOVE_MEMBER = 'command_namespace_remove_member';
    case COMMAND_NAMESPACE_REMOVE_ADMIN = 'command_namespace_remove_admin';
    case TYPE_ATTRIBUTE_REQUIRED = 'type_attribute_required';
    case MEMBERSHIP_AFFINITY = 'membership_affinity';
    case WRITE = 'write';
    case READ = 'read';

    public static function tryFromInput(string|int|bool|null $test ) : RuleTargetActionType {
        $maybe  = RuleTargetActionType::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(RuleTargetActionType::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

}


