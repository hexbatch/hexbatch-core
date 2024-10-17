<?php

namespace App\Enums\Sys;

/*
 * reading multiple matches will use its merge method to merge the multiple targets
 * writing or commanding multiple will do that for each found
 *

 * if writing/commanding/targeting large amounts, then will paginate the things, once a page is done, will do another, but in increasing the wait time between each page
 *  the result uses a cursor in things
 *  The page amount is in rule_target_page_size for the server events table for the event this rule is triggered by (all rules fired by events)
 *
 *  if reading very large results, then the same pagination happens, and each page has merged data from that page sent through the rule
 */
enum TypeOfAction: string
{

    case PRAGMA_WRITE_VISUAL_SHAPE = 'pragma_write_visual_shape'; //if the attribute has a shape, set the visual section to one or more opacity|color|border|texture
    case PRAGMA_WRITE_VISUAL_MAP = 'pragma_write_visual_map'; //if the type has a map set display properties

    case PRAGMA_WRITE = 'pragma_write'; //to the element attribute
    case PRAGMA_READ = 'pragma_read'; //from the element attribute

    case PRAGMA_READ_TIME = 'pragma_read_bounds_time'; //the start and stop time of the type that owns the element
    case PRAGMA_READ_SHAPE = 'pragma_read_bounds_shape'; //the shape of the attribute being read, includes display
    case PRAGMA_READ_MAP = 'pragma_read_bounds_map'; //the map of the type that owns the element, includes display




    case PRAGMA_ELEMENT_ON = 'pragma_element_on';
    case PRAGMA_ELEMENT_TOGGLE = 'pragma_element_toggle';
    case PRAGMA_ELEMENT_OFF = 'pragma_element_off';
    case PRAGMA_TYPE_ON = 'pragma_element_type_on';
    case PRAGMA_TYPE_TOGGLE = 'pragma_element_type_toggle';
    case PRAGMA_TYPE_OFF = 'pragma_element_type_off';

    case PRAGMA_SEARCH = 'pragma_search';

    case OP_COMBINE = 'op_combine';
    case OP_MUTUAL = 'op_mutual';
    case OP_POP = 'op_pop';
    case OP_PUSH = 'op_push';
    case OP_SHIFT = 'op_shift';
    case OP_UNSHIFT = 'op_unshift';






    case CMD_DESIGN_PARENT_ADD = 'cmd_design_parent_add';
    case CMD_DESIGN_PARENT_REMOVE = 'cmd_design_parent_remove';
    case CMD_DESIGN_TYPE_MAP = 'cmd_design_type_map';
    case CMD_DESIGN_TYPE_TIME = 'cmd_design_type_time';
    case CMD_DESIGN_TYPE_EDIT = 'cmd_design_type_edit';

    case CMD_DESIGN_ATTRIBUTE_CREATE = 'cmd_design_attribute_create';
    case CMD_DESIGN_ATTRIBUTE_REMOVE = 'cmd_design_attribute_remove';
    case CMD_DESIGN_ATTRIBUTE_EDIT = 'cmd_design_attribute_edit';
    case CMD_DESIGN_ATTRIBUTE_HANDLE = 'cmd_design_attribute_handle';
    case CMD_DESIGN_LISTENER_CREATE = 'cmd_design_listener_create';
    case CMD_DESIGN_LISTENER_DESTROY = 'cmd_design_listener_destroy';
    case CMD_DESIGN_RULE_ADD = 'cmd_design_rule_add';
    case CMD_DESIGN_RULE_REMOVE = 'cmd_design_rule_remove';
    case CMD_DESIGN_RULE_EDIT = 'cmd_design_rule_edit';
    case CMD_DESIGN_RULE_TEST = 'cmd_design_rule_test';

    case CMD_TYPE_CREATE = 'cmd_type_create';
    case CMD_TYPE_PUBLISH = 'cmd_type_publish';
    case CMD_TYPE_SUSPEND = 'cmd_type_suspend';
    case CMD_TYPE_RETIRE = 'cmd_type_retire';
    case CMD_TYPE_DESTROY = 'cmd_type_destroy';
    case CMD_TYPE_DESTROY_NO_EVENTS = 'cmd_type_destroy_no_events'; //server admin
    case CMD_TYPE_CHANGE_OWNER = 'cmd_type_change_owner';

    case CMD_TYPE_HANDLE_ADD = 'cmd_type_handle_add';
    case CMD_TYPE_HANDLE_REMOVE = 'cmd_type_handle_remove';

    case CMD_ATTRIBUTE_DESCRIPTION_ADD = 'cmd_attribute_description_add';
    case CMD_ATTRIBUTE_DESCRIPTION_REMOVE = 'cmd_attribute_description_remove';

    case CMD_PATH_CREATE = 'cmd_path_create';
    case CMD_PATH_EDIT = 'cmd_path_edit';
    case CMD_PATH_COPY = 'cmd_path_copy';
    case CMD_PATH_DESTROY = 'cmd_path_destroy';
    case CMD_PATH_HANDLE_ADD = 'cmd_path_handle_add';
    case CMD_PATH_HANDLE_REMOVE = 'cmd_path_handle_remove';
    case CMD_SET_CREATE = 'cmd_set_create'; //child set or top level set
    case CMD_SET_DESTROY = 'cmd_set_destroy'; //if admin of definer element of set ns
    case CMD_SET_DESTROY_NO_EVENTS = 'cmd_set_destroy_no_events'; //server admin
    case CMD_LINK_ADD = 'cmd_link_add';
    case CMD_LINK_REMOVE = 'cmd_link_remove';


    case CMD_SET_MEMBER_ADD = 'cmd_set_member_add'; //add element to set
    case CMD_SET_MEMBER_REMOVE = 'cmd_set_member_remove'; //add element to set

    case CMD_SET_MEMBER_ADD_NO_EVENTS = 'cmd_set_member_add_no_events'; //add element to set
    case CMD_SET_MEMBER_REMOVE_NO_EVENTS = 'cmd_set_member_remove_no_events'; //add element to set

    case CMD_SET_CHILD_HANDLE_ADD = 'cmd_set_child_handle_add';
    case CMD_SET_CHILD_HANDLE_REMOVE = 'cmd_set_child_handle_remove';


    case CMD_ELEMENT_CREATE = 'cmd_element_create';
    case CMD_ELEMENT_CHANGE_OWNER = 'cmd_element_change_owner';

    case CMD_ELEMENT_DESTROY = 'cmd_element_destroy';
    case CMD_ELEMENT_DESTROY_NO_EVENTS = 'cmd_element_destroy_no_events'; //server admin
    case CMD_LIVE_TYPE_ADD = 'cmd_live_type_add';
    case CMD_LIVE_TYPE_COPY = 'cmd_live_type_copy';
    case CMD_LIVE_TYPE_REMOVE = 'cmd_live_type_remove';
    case CMD_LIVE_TYPE_TOGGLE = 'cmd_live_type_toggle';

    case CMD_FILTER_ADD = 'cmd_filter_add';
    case CMD_FILTER_REMOVE = 'cmd_filter_remove';

    case CMD_FILTER_GLOBAL_ADD = 'cmd_filter_global_add';
    case CMD_FILTER_GLOBAL_REMOVE = 'cmd_filter_global_remove';


    case CMD_FIRE_CUSTOM_EVENT = 'cmd_fire_custom_event'; //scope depends on the base type of the custom event

    /*
     ns things can get list of ns from path of this rule and piped in ns from children
     */
    case CMD_NAMESPACE_MEMBER_ADD = 'cmd_namespace_member_add';

    case CMD_NAMESPACE_ADMIN_ADD = 'cmd_namespace_admin_add';
    case CMD_NAMESPACE_ADMIN_REMOVE = 'cmd_namespace_admin_remove';
    case CMD_NAMESPACE_MEMBER_REMOVE = 'cmd_namespace_remove_member';


    case CMD_NAMESPACE_CREATE = 'cmd_namespace_create'; //logged in ns is owner
    case CMD_NAMESPACE_DESTROY = 'cmd_namespace_destroy'; //the owner, cannot destroy default ns
    case CMD_NAMESPACE_DESTROY_NO_EVENTS = 'cmd_namespace_destroy_no_events'; //server admin

    case CMD_NAMESPACE_USER_ASSIGN = 'cmd_namespace_user_assign'; //only system and if the ns is not a default for current users

    case CMD_SEMAPHORE_READY = 'cmd_semaphore_ready';
    case CMD_SEMAPHORE_RESET = 'cmd_semaphore_reset';
    case CMD_SEMAPHORE_MASTER_CREATE = 'cmd_semaphore_master_create';
    case CMD_SEMAPHORE_MASTER_RUN = 'cmd_semaphore_master_run';

    case CMD_RUN_REMOTE = 'cmd_run_remote'; //if admin of type ns, makes outgoing remote

    //server to server
    case CMD_ELSEWHERE_GIVE_ELEMENT = 'cmd_elsewhere_give_element';
    case CMD_ELSEWHERE_GIVE_TYPE = 'cmd_elsewhere_give_type';
    case CMD_ELSEWHERE_GIVE_NS = 'cmd_elsewhere_give_ns';
    case CMD_ELSEWHERE_GIVE_SET = 'cmd_elsewhere_give_set';
    case CMD_ELSEWHERE_DO_REGISTRATION = 'cmd_elsewhere_do_registration';
    case CMD_ELSEWHERE_CHANGE_STATUS = 'cmd_elsewhere_change_status';
    case CMD_ELSEWHERE_PURGE = 'cmd_elsewhere_purge'; //removes all types,ns,ele,sets associated with server, no events



    //adjust thing limits, and back-offs, this can be run as part of a chain, only the system admin can run these
    case CMD_THING_RATE_LIMIT = 'cmd_thing_rate_limit';
    case CMD_THING_PAGE_SIZE = 'cmd_thing_page_size';
    case CMD_THING_DEPTH_LIMIT = 'cmd_thing_depth_limit';
    case CMD_THING_JSON_SIZE = 'cmd_thing_json_size';
    case CMD_THING_BACKOFF_RATE = 'cmd_thing_backoff_rate';




    public static function tryFromInput(string|int|bool|null $test ) : TypeOfAction {
        $maybe  = TypeOfAction::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfAction::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

}



