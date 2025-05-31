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

    case NOP = 'NOP';
    case BASE_ACTION = 'base_action';
    case BASE_COMMAND = 'base_command';
    case BASE_PRAGMA = 'base_pragma';
    case BASE_OPERATION = 'base_op';

    case BASE_NAMESPACE = 'base_namespace';
    case BASE_ELSEWHERE = 'base_elsewhere';
    case BASE_SERVER = 'base_server';
    case BASE_TIME = 'base_time';
    case BASE_ELEMENT = 'base_element';
    case BASE_DESIGN = 'base_design';
    case BASE_PATH = 'base_path';
    case BASE_PHASE = 'base_phase';
    case BASE_SET = 'base_set';
    case BASE_TYPE = 'base_type';
    case BASE_USER = 'base_user';
    case BASE_WAIT = 'base_wait';



    case PRAGMA_WRITE = 'pragma_write'; //to the element attribute
    case PRAGMA_READ = 'pragma_read'; //from the element attribute
    case PRAGMA_READ_TYPE = 'pragma_read_type';
    case PRAGMA_READ_LIVE_TYPE = 'pragma_read_live_type';

    case PRAGMA_READ_TIME_SPAN = 'pragma_read_time_span'; //the start and stop time of the type that owns the element (current time span)
    case PRAGMA_READ_VISUAL = 'pragma_read_visual'; //the shape of the attribute being read, includes display




    case PRAGMA_ELEMENT_ON = 'pragma_element_on';
    case PRAGMA_ELEMENT_OFF = 'pragma_element_off';
    case PRAGMA_TYPE_ON = 'pragma_element_type_on';
    case PRAGMA_TYPE_OFF = 'pragma_element_type_off';

    case PRAGMA_SEARCH = 'pragma_search';

    case OP_COMBINE = 'op_combine';
    case OP_MUTUAL = 'op_mutual';
    case OP_POP = 'op_pop';
    case OP_PUSH = 'op_push';
    case OP_SHIFT = 'op_shift';
    case OP_UNSHIFT = 'op_unshift';




    case CMD_DESIGN_CREATE = 'cmd_type_create';
    case CMD_DESIGN_DESTROY = 'cmd_design_destroy';
    case CMD_DESIGN_PURGE = 'cmd_design_purge';



    case CMD_DESIGN_EDIT = 'cmd_design_edit';


    case CMD_DESIGN_PARENT_ADD = 'cmd_design_parent_add';
    case CMD_DESIGN_PARENT_REMOVE = 'cmd_design_parent_remove';

    case CMD_DESIGN_TIME_CREATE = 'cmd_design_time_create';
    case CMD_DESIGN_TIME_EDIT = 'cmd_design_time_edit';


    case CMD_DESIGN_LOCATION_CREATE = 'cmd_design_location_create';
    case CMD_DESIGN_LOCATION_EDIT = 'cmd_design_location_edit';
    case CMD_DESIGN_LOCATION_DESTROY = 'cmd_design_location_destroy';
    case CMD_DESIGN_TIME_DESTROY = 'cmd_design_time_destroy';

    case CMD_DESIGN_ATTRIBUTE_CREATE = 'cmd_design_attribute_create';
    case CMD_DESIGN_ATTRIBUTE_PROMOTE = 'cmd_design_attribute_promote';
    case CMD_DESIGN_ATTRIBUTE_DESTROY = 'cmd_design_attribute_remove';
    case CMD_DESIGN_ATTRIBUTE_EDIT = 'cmd_design_attribute_edit';
    case CMD_DESIGN_LISTENER_CREATE = 'cmd_design_listener_create';
    case CMD_DESIGN_LISTENER_DESTROY = 'cmd_design_listener_destroy';
    case CMD_DESIGN_RULE_CREATE = 'cmd_design_rule_create';
    case CMD_DESIGN_RULE_DESTROY = 'cmd_design_rule_destroy';
    case CMD_DESIGN_RULE_EDIT = 'cmd_design_rule_edit';
    case CMD_DESIGN_LISTENER_TEST = 'cmd_design_listener_test';

    case CMD_DESIGN_LIVE_RULE_ADD = 'cmd_design_live_rule_add';
    case CMD_DESIGN_LIVE_RULE_REMOVE = 'cmd_design_live_rule_remove';


    case CMD_DESIGN_REQUIREMENT_ADD = 'cmd_design_requirement_add';
    case CMD_DESIGN_REQUIREMENT_REMOVE = 'cmd_design_requirement_remove';

    case CMD_DESIGN_OWNER_CHANGE = 'cmd_design_owner_change';
    case CMD_DESIGN_OWNER_PROMOTE = 'cmd_design_owner_promote';


    case CMD_TYPE_PUBLISH = 'cmd_type_publish';
    case CMD_TYPE_PUBLISH_PROMOTE = 'cmd_type_publish_promote';
    case CMD_TYPE_SUSPEND = 'cmd_type_suspend';
    case CMD_TYPE_RETIRE = 'cmd_type_retire';
    case CMD_TYPE_DESTROY = 'cmd_type_destroy';
    case CMD_TYPE_PURGE = 'cmd_type_purge'; //server admin
    case CMD_TYPE_OWNER_CHANGE = 'cmd_type_owner_change';
    case CMD_TYPE_OWNER_PROMOTE = 'cmd_type_owner_promote';

    case CMD_TYPE_HANDLE_ADD = 'cmd_type_handle_add';
    case CMD_TYPE_HANDLE_REMOVE = 'cmd_type_handle_remove';

    case CMD_ATTRIBUTE_HANDLE_ADD = 'cmd_attribute_handle_add';
    case CMD_ATTRIBUTE_HANDLE_REMOVE = 'cmd_attribute_handle_remove';

    case CMD_PATH_CREATE = 'cmd_path_create';
    case CMD_PATH_EDIT = 'cmd_path_edit';
    case CMD_PATH_COPY = 'cmd_path_copy';
    case CMD_PATH_DESTROY = 'cmd_path_destroy';
    case CMD_PATH_TEST = 'cmd_path_test';
    case CMD_PATH_PUBLISH = 'cmd_path_publish';
    case CMD_PATH_HANDLE_ADD = 'cmd_path_handle_add';
    case CMD_PATH_HANDLE_REMOVE = 'cmd_path_handle_remove';

    case CMD_PATH_PART_CREATE = 'cmd_path_part_create';
    case CMD_PATH_PART_DESTROY = 'cmd_path_part_destroy';
    case CMD_PATH_PART_EDIT = 'cmd_path_part_edit';
    case CMD_PATH_PART_TEST = 'cmd_path_part_test';


    case CMD_SET_CREATE = 'cmd_set_create'; //child set or top level set
    case CMD_SET_PROMOTE = 'cmd_set_promote'; //child set or top level set
    case CMD_SET_DESTROY = 'cmd_set_destroy'; //if ns-admin of definer element of set ns

    case CMD_SET_EMPTY = 'cmd_set_empty'; //non sticky stuff
    case CMD_SET_PURGE = 'cmd_set_purge'; //server admin
    case CMD_LINK_ADD = 'cmd_link_add';
    case CMD_LINK_REMOVE = 'cmd_link_remove';


    case CMD_SET_MEMBER_ADD = 'cmd_set_member_add'; //add element to set
    case CMD_SET_MEMBER_STICK = 'cmd_set_member_stick';
    case CMD_SET_MEMBER_UNSTICK = 'cmd_set_member_unstick';
    case CMD_SET_MEMBER_REMOVE = 'cmd_set_member_remove';

    case CMD_SET_MEMBER_PROMOTE = 'cmd_set_member_promote'; //add element to set, no events
    case CMD_SET_MEMBER_PURGE = 'cmd_set_member_purge'; //remove element from set, no events


    case CMD_ELEMENT_PING = 'cmd_element_ping';


    case CMD_ELEMENT_PROMOTE = 'cmd_element_promote';
    case CMD_ELEMENT_EDIT = 'cmd_element_edit';
    case CMD_ELEMENT_CREATE = 'cmd_element_create';
    case CMD_ELEMENT_CHANGE_OWNER = 'cmd_element_change_owner';

    case CMD_ELEMENT_DESTROY = 'cmd_element_destroy';
    case CMD_ELEMENT_PURGE = 'cmd_element_purge'; //server admin
    case CMD_LIVE_TYPE_ADD = 'cmd_live_type_add';
    case CMD_LIVE_TYPE_PROMOTE = 'cmd_live_type_promote';
    case CMD_LIVE_TYPE_DEMOTE = 'cmd_live_type_demote';
    case CMD_LIVE_TYPE_COPY = 'cmd_live_type_copy';
    case CMD_LIVE_TYPE_REMOVE = 'cmd_live_type_remove';


    case CMD_TIME_SPAN_ROTATION = 'cmd_time_span_rotation';
    case CMD_PHASE_CUT_TREE = 'cmd_phase_cut_tree';
    case CMD_PHASE_REPLACE_TREE = 'cmd_phase_replace_tree';
    case CMD_PHASE_MOVE_TREE = 'cmd_phase_move_tree';

    case CMD_PHASE_PURGE = 'cmd_phase_purge';
    case CMD_PHASE_CREATE = 'cmd_phase_create';

    case CMD_FIRE_CUSTOM_EVENT = 'cmd_fire_custom_event'; //scope depends on the base type of the custom event

    /*
     ns things can get list of ns from path of this rule and piped in ns from children
     */
    case CMD_NAMESPACE_MEMBER_ADD = 'cmd_namespace_member_add';

    case CMD_NAMESPACE_ADMIN_ADD = 'cmd_namespace_admin_add';
    case CMD_NAMESPACE_ADMIN_REMOVE = 'cmd_namespace_admin_remove';
    case CMD_NAMESPACE_MEMBER_REMOVE = 'cmd_namespace_remove_member';


    case CMD_NAMESPACE_MEMBER_PROMOTE = 'cmd_namespace_member_promote';
    case CMD_NAMESPACE_ADMIN_PROMOTE = 'cmd_namespace_admin_promote';
    case CMD_NAMESPACE_ADMIN_PURGE = 'cmd_namespace_admin_purge';
    case CMD_NAMESPACE_MEMBER_PURGE = 'cmd_namespace_member_purge';




    case CMD_NAMESPACE_CREATE = 'cmd_namespace_create'; //logged in ns is owner
    case CMD_NAMESPACE_PREP_TRANSFER = 'cmd_namespace_prep_transfer';
    case CMD_NAMESPACE_DO_TRANSFER = 'cmd_namespace_do_transfer';
    case CMD_NAMESPACE_PROMOTE = 'cmd_namespace_promote';
    case CMD_NAMESPACE_EDIT = 'cmd_namespace_edit';
    case CMD_NAMESPACE_DESTROY = 'cmd_namespace_destroy'; //the owner, cannot destroy default ns
    case CMD_NAMESPACE_PURGE = 'cmd_namespace_purge'; //server admin


    case CMD_NAMESPACE_HANDLE_ADD = 'cmd_namespace_handle_add';
    case CMD_NAMESPACE_HANDLE_REMOVE = 'cmd_namespace_handle_remove';


    case CMD_USER_PREPARE_DELETION = 'cmd_user_prepare_deletion';
    case CMD_USER_START_DELETION = 'cmd_user_start_deletion';
    case CMD_USER_REGISTER = 'cmd_user_register';
    case CMD_USER_LOGIN = 'cmd_user_login';
    case CMD_USER_EDIT = 'cmd_user_edit';


    case CMD_WAIT_ALL = 'cmd_wait_all';
    case CMD_WAIT_ANY = 'cmd_wait_any';
    case CMD_WAIT_AVAILABLE = 'cmd_wait_available';
    case CMD_WAIT_MUTEX = 'cmd_wait_mutex';
    case CMD_WAIT_SEMAPHORE = 'cmd_wait_semaphore';
    case CMD_SEMAPHORE_READY = 'cmd_semaphore_ready';
    case CMD_SEMAPHORE_RESET = 'cmd_semaphore_reset';
    case CMD_SEMAPHORE_MASTER_CREATE = 'cmd_semaphore_master_create';
    case CMD_SEMAPHORE_MASTER_RUN = 'cmd_semaphore_master_run';
    case CMD_SEMAPHORE_MASTER_UPDATE = 'cmd_semaphore_master_update';

    case CMD_SERVER_PROMOTE = 'cmd_server_promote';
    case CMD_SERVER_EDIT = 'cmd_server_edit';
    case CMD_SERVER_SHOW = 'cmd_server_show';
    case CMD_SERVER_SHOW_ADMIN = 'cmd_server_show_admin';


    //server to server
    case CMD_ELSEWHERE_GIVE_ELEMENT = 'cmd_elsewhere_give_element'; //done in the ns of the server asking for this and below until admin area
    case CMD_ELSEWHERE_GIVE_TYPE = 'cmd_elsewhere_give_type';
    case CMD_ELSEWHERE_GIVE_NS = 'cmd_elsewhere_give_ns';
    case CMD_ELSEWHERE_GIVE_SET = 'cmd_elsewhere_give_set';
    case CMD_ELSEWHERE_GIVE_EVENT = 'cmd_elsewhere_give_event';
    case CMD_ELSEWHERE_SHARING_ELEMENT = 'cmd_elsewhere_sharing_element';
    case CMD_ELSEWHERE_DESTROYED_ELEMENT = 'cmd_elsewhere_destroyed_element';
    case CMD_ELSEWHERE_SUSPENDED_TYPE = 'cmd_elsewhere_suspended_type';

    //admin area starts
    case CMD_ELSEWHERE_DO_REGISTRATION = 'cmd_elsewhere_do_registration'; //system admin or group only for this or below
    case CMD_ELSEWHERE_GIVE_CREDENTIALS = 'cmd_elsewhere_give_credentials';
    case CMD_ELSEWHERE_PUSH_CREDENTIALS = 'cmd_elsewhere_push_credentials';
    case CMD_ELSEWHERE_ASK_CREDENTIALS = 'cmd_elsewhere_ask_credentials';
    case CMD_ELSEWHERE_ASK_ELEMENT = 'cmd_elsewhere_ask_element';
    case CMD_ELSEWHERE_ASK_TYPE = 'cmd_elsewhere_ask_type';
    case CMD_ELSEWHERE_ASK_SET = 'cmd_elsewhere_ask_set';
    case CMD_ELSEWHERE_ASK_NAMESPACE = 'cmd_elsewhere_ask_namespace';
    case CMD_ELSEWHERE_CHANGE_STATUS = 'cmd_elsewhere_change_status';
    case CMD_ELSEWHERE_PURGE = 'cmd_elsewhere_purge'; //removes all types,ns,ele,sets associated with server, no events

    case CMD_ELSEWHERE_PUSH_ELEMENT = 'cmd_elsewhere_push_element';
    case CMD_ELSEWHERE_PUSH_SET = 'cmd_elsewhere_push_set';
    case CMD_ELSEWHERE_PUSH_TYPE = 'cmd_elsewhere_push_type';
    case CMD_ELSEWHERE_PUSH_NAMESPACE = 'cmd_elsewhere_push_namespace';
    case CMD_ELSEWHERE_PUSH_EVENT = 'cmd_elsewhere_push_event';


    public static function tryFromInput(string|int|bool|null $test ) : TypeOfAction {
        $maybe  = TypeOfAction::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfAction::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

}



