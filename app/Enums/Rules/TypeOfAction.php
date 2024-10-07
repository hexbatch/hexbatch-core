<?php

namespace App\Enums\Rules;
/**
 * postgres enum rule_target_action_type
 */
enum TypeOfAction: string
{
//todo these are made into types for mini-api

    case PRAGMA_SHAPE_OFFSET = 'pragma_shape_offset';
    case PRAGMA_SHAPE_ROTATION = 'pragma_shape_rotation';
    case PRAGMA_SHAPE_COLOR = 'pragma_shape_color';
    case PRAGMA_SHAPE_TEXTURE = 'pragma_shape_texture';
    case PRAGMA_SHAPE_OPACITY = 'pragma_shape_opacity';
    case PRAGMA_SHAPE_ZORDER = 'pragma_shape_zorder';
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
    case COMMAND_REMOVE_FROM_SET = 'command_remove_from_set';
    case COMMAND_DESTROY_NAMESPACE = 'command_destroy_namespace';
    case COMMAND_DESTROY_USER = 'command_destroy_user';
    case COMMAND_ASSIGN_USER_TO_NAMESPACE = 'command_assign_user_to_namespace';
    case COMMAND_CREATE_ELEMENT = 'command_create_element';
    case COMMAND_DESTROY_ELEMENT = 'command_destroy_element';
    case COMMAND_ADD_LIVE_TYPE_ON_ELEMENT = 'command_add_live_type_on_element';
    case COMMAND_REMOVE_LIVE_TYPE_ON_ELEMENT = 'command_remove_live_type_on_element';
    case COMMAND_NAMESPACE_ADD_MEMBER = 'command_namespace_add_member';
    case COMMAND_NAMESPACE_ADD_ADMIN = 'command_namespace_add_admin';
    case COMMAND_NAMESPACE_REMOVE_MEMBER = 'command_namespace_remove_member';
    case COMMAND_NAMESPACE_REMOVE_ADMIN = 'command_namespace_remove_admin';
    case TYPE_ATTRIBUTE_REQUIRED = 'type_attribute_required';
    case MEMBERSHIP_AFFINITY = 'membership_affinity';
    case WRITE = 'write';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfAction {
        $maybe  = TypeOfAction::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfAction::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

}

/*

'command_destroy_namespace', -- server user or ns owner
'command_destroy_user', -- server user
'command_assign_user_to_namespace', -- server user
'command_create_element', -- single only
'command_add_live_type_element', -- type(s) found in the data path
'command_namespace_add_member', -- namespace found by the type of the attribute chosen,server user or ns owner

 */


