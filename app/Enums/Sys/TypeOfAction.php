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

    case PRAGMA_SHAPE_VISUAL = 'pragma_shape_visual'; //if the attribute has a shape, set the visual section to one or more opacity|color|border|texture

    case PRAGMA_WRITE = 'pragma_write'; //to the element attribute
    case PRAGMA_READ = 'pragma_read'; //from the element attribute

    case PRAGMA_READ_BOUNDS_TIME = 'pragma_read_bounds_time'; //the start and stop time of the type that owns the element
    case PRAGMA_READ_BOUNDS_SHAPE = 'pragma_read_bounds_shape'; //the shape of the attribute being read
    case PRAGMA_READ_BOUNDS_MAP = 'pragma_read_bounds_map'; //the map of the type that owns the element




    case PRAGMA_ELEMENT_ON = 'pragma_element_on';
    case PRAGMA_ELEMENT_TOGGLE = 'pragma_element_toggle';
    case PRAGMA_ELEMENT_OFF = 'pragma_element_off';
    case PRAGMA_ELEMENT_TYPE_ON = 'pragma_element_type_on';
    case PRAGMA_ELEMENT_TYPE_TOGGLE = 'pragma_element_type_toggle';
    case PRAGMA_ELEMENT_TYPE_OFF = 'pragma_element_type_off';






    case COMMAND_RUN_REMOTE = 'command_run_remote'; //if admin of type ns, makes outgoing remote
    case COMMAND_LISTEN_REMOTE = 'command_listen_remote'; //if admin of type ns, this will wait until the server is contacted with information from the remote incoming
    case COMMAND_MAKE_SET = 'command_make_set'; //child set or top level set
    case COMMAND_DESTROY_SET = 'command_destroy_set'; //if admin of definer element of set ns
    case COMMAND_ADD_TO_SET = 'command_add_to_set';  //if admin of definer element of set ns
    case COMMAND_REMOVE_FROM_SET = 'command_remove_from_set'; //removes element from the target set
    case COMMAND_DESTROY_NAMESPACE = 'command_destroy_namespace'; //the owner, and if not default
    case COMMAND_DESTROY_USER = 'command_destroy_user'; //only system
    case COMMAND_ASSIGN_USER_TO_NAMESPACE = 'command_assign_user_to_namespace'; //only system and if the ns is not a default for current users



    case COMMAND_CREATE_ELEMENT = 'command_create_element';
    case COMMAND_CHANGE_ELEMENT_OWNER = 'command_change_element_owner';
    case COMMAND_CHANGE_TYPE_OWNER = 'command_change_type_owner';
    case COMMAND_DESTROY_ELEMENT = 'command_destroy_element';
    case COMMAND_ADD_LIVE_TYPE_ON_ELEMENT = 'command_add_live_type_on_element';
    case COMMAND_REMOVE_LIVE_TYPE_ON_ELEMENT = 'command_remove_live_type_on_element';

    case COMMAND_ADD_LIVE_SHAPE_ON_ELEMENT = 'command_add_live_shape_on_element';
    case COMMAND_REMOVE_LIVE_SHAPE_ON_ELEMENT = 'command_remove_live_shape_on_element';

    case COMMAND_SHAPE_EVENT_BLOCKING_ON = 'command_shape_event_blocking_on'; // blocks set scope events based on z order of intersection
    case COMMAND_SHAPE_EVENT_BLOCKING_OFF = 'command_shape_event_blocking_off'; // unblocks  for event or all set scoped events

    case COMMAND_FIRE_CUSTOM_EVENT_SCOPED_ELEMENT = 'command_fire_custom_event_scoped_element'; //any types based on
    case COMMAND_FIRE_CUSTOM_EVENT_SCOPED_SET = 'command_fire_custom_event_scoped_set';
    case COMMAND_FIRE_CUSTOM_EVENT_SCOPE_CHAINED = 'command_fire_custom_event_scope_chained';
    case COMMAND_FIRE_CUSTOM_EVENT = 'command_fire_custom_event'; //scope depends on the base type of the custom event

    /*
     ns things can get list of ns from path of this rule and piped in ns from children
     */
    case COMMAND_NAMESPACE_ADD_MEMBER = 'command_namespace_add_member';
    case COMMAND_NAMESPACE_ADD_ADMIN = 'command_namespace_add_admin';
    case COMMAND_NAMESPACE_REMOVE_MEMBER = 'command_namespace_remove_member';
    case COMMAND_NAMESPACE_REMOVE_ADMIN = 'command_namespace_remove_admin';



    //server to server
    case CMD_ELSEWHERE_GIVE_ELEMENT = 'cmd_elsewhere_give_element';
    case CMD_ELSEWHERE_GIVE_TYPE = 'cmd_elsewhere_give_type';
    case CMD_ELSEWHERE_GIVE_NS = 'cmd_elsewhere_give_ns';
    case CMD_ELSEWHERE_GIVE_SET = 'cmd_elsewhere_give_set';
    case CMD_ELSEWHERE_DO_REGISTRATION = 'cmd_elsewhere_do_registration';
    case CMD_ELSEWHERE_CHANGE_STATUS = 'cmd_elsewhere_change_status';
    case CMD_ELSEWHERE_UNREGISTER = 'cmd_elsewhere_unregister';



    //adjust thing limits, and back-offs, this can be run as part of a chain, only the system admin can run these
    case CMD_THING_SET_RATE_LIMIT = 'cmd_thing_set_rate_limit';
    case CMD_THING_SET_PAGE_SIZE = 'cmd_thing_set_page_size';
    case CMD_THING_SET_DEPTH_LIMIT = 'cmd_thing_set_depth_limit';
    case CMD_THING_SET_JSON_SIZE = 'cmd_thing_set_json_size';
    case CMD_THING_SET_BACKOFF_RATE = 'cmd_thing_set_backoff_rate';




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


