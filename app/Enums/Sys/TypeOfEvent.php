<?php

namespace App\Enums\Sys;

/*
 * Events can run before or after a successful event
 * if running after, then member of ns of type
 *
 * rule handlers can listen to a huge number of events at one time
 * if event rules are sent large amount of stuff happening,
 *    then will paginate the things, once a page is done, will do another, but in increasing the wait time between each page
 * the page size can be set in the server events for that type and event
 *
 * events handling large data will not complete until all the pagination is done, but
 *  because there is an exponential backoff strategy for each new page, events that wait on many things may not complete for a long time (minutes, hours, days)
 *  this will inspire more targeted rules
 *
 * Also use backoff rate if too many rule commands from same namespace, this is configured , other than default, as a
 *
 */

enum TypeOfEvent: string
{
    case NOTHING = 'nothing';


    /*
    _.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-._
    "`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'"
     */

    //scoped to sets and elements participating in the group events

    case GROUP_OPERATION = 'group_operation'; //add each set operation here?
    case SET_OPERATION = 'set_operation';  //do I need both?




    /*
    _.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-._
    "`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'"
     */
    //scoped same element

    //in path element
    case SEARCH_RESULTS = 'search_results'; //ns member of element



    //fired only on the ns private element
    case NAMESPACE_MEMBER_ADD = 'namespace_member_add';
    case NAMESPACE_ADDING_ADMIN = 'namespace_adding_admin';
    case NAMESPACE_REMOVING_MEMBER = 'namespace_removing_member';
    case NAMESPACE_REMOVING_ADMIN = 'namespace_removing_admin';




    /*
    _.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-._
    "`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'"
     */
    //scoped to same set

    case ATTRIBUTE_READ = 'attribute_read';
    case ATTRIBUTE_WRITE = 'attribute_write';

    case ATTRIBUTE_TURNED_OFF = 'attribute_turned_off';
    case ATTRIBUTE_TURNED_ON = 'attribute_turned_on';
    case ATTRIBUTES_TURNED_OFF = 'attributes_turned_off';



    case SET_CONTENTS_SHAPE_CHANGED = 'set_contents_shape_changed';

    case SET_ENTER = 'set_enter';
    case SET_LEAVE = 'set_leave';

    case SET_CHILD_CREATED = 'set_child_created';
    case SET_CHILD_DESTROYED = 'set_child_destroyed';

    case SET_CREATED = 'set_created';

    case SET_DESTROYED = 'set_destroyed';

    case SET_LINK_CREATED = 'set_link_created';
    case SET_LINK_DESTROYED = 'set_link_destroyed';


    case SHAPE_INTERSECTION_ENTER = 'shape_intersection_enter';
    case SHAPE_INTERSECTION_LEAVE = 'shape_intersection_leave';

    case TYPE_LIVE_ADDED = 'type_live_added'; //these two live events go to both types
    case TYPE_LIVE_REMOVED = 'type_live_removed';


    /*
    _.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-._
    "`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'"
     */
    //scoped to the ancestor chain of sets

    case REMOTE_SUCCESS = 'remote_success'; //type ns members
    case REMOTE_FAIL = 'remote_fail'; //type ns members
    case REMOTE_ALWAYS = 'remote_always'; //type ns members




    /*
  _.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-._
  "`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'"
   */
    //scoped to changes in the type or attribute definition


    case ELEMENT_CREATION = 'element_creation'; //type or type ancestor ns admin
    case ELEMENT_BATCH_CREATION = 'element_batch_creation'; //type or type ancestor ns admin

    case ELEMENT_DESTRUCTION = 'element_destruction'; //type or type ancestor ns admin

    case ELEMENT_REENTERED = 'element_reentered'; //element with same uuid come back after copied out

    case TYPE_PUBLISHED = 'type_published'; //covers both parent types and parent attributes: type or type ancestor ns admin
    case TYPE_RETIRED = 'type_retired'; //type or type ancestor ns admin
    case TYPE_SUSPENDED = 'type_suspended'; //only system admin group
    case TYPE_DELETED = 'type_deleted'; //only system admin group




    /*
    _.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-._
    "`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'"
     */
    //system wide
    case RUN_REMOTE = 'run_remote'; //a remote is about to be called
    case LISTEN_REMOTE = 'listen_remote'; //waiting for incoming call to the server

    case SET_TOP_LEVEL_DESTROYED = 'set_top_level_destroyed';
    case SET_TOP_LEVEL_CREATED = 'set_top_level_created';


    /*
   _.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-._
   "`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'"
    */
    //system wide server stuff


    case PUSH_ELEMENT_ELSEWHERE = 'push_element_elsewhere';
    case PUSH_SET_ELSEWHERE = 'push_set_elsewhere';
    case PUSH_TYPE_ELSEWHERE = 'push_type_elsewhere';
    case PUSH_NS_ELSEWHERE = 'push_ns_elsewhere';
    case RUN_EVENT_ELSEWHERE = 'run_event_elsewhere';

    case NEW_ELSEWHERE_ELEMENT = 'new_elsewhere_element';
    case NEW_ELSEWHERE_SET = 'new_elsewhere_set';
    case NEW_ELSEWHERE_EVENT = 'new_elsewhere_event';
    case NEW_ELSEWHERE_TYPE = 'new_elsewhere_type';
    case NEW_ELSEWHERE_NS = 'new_elsewhere_ns';
    case NEW_ELSEWHERE_KEY = 'new_elsewhere_key';


    case ELSEWHERE_SUSPENDED_TYPE = 'elsewhere_suspended_type';
    case ELSEWHERE_DESTROYED_ELEMENT = 'elsewhere_destroyed_element';
    case SHARING_ELSEWHERE_ELEMENT = 'sharing_elsewhere_element';
    case PUSH_TO_NEXT_SERVER = 'push_to_next_server';


    case SERVER_REGISTERED = 'server_registered';
    case SERVER_UNREGISTERED = 'server_unregistered';
    case SERVER_STATUS_ALLOWED = 'server_status_allowed';
    case SERVER_STATUS_DENIED = 'server_status_denied';
    case SERVER_STATUS_PAUSED = 'server_status_paused';
    case SERVER_REGENERATE_SERVER_KEY = 'server_regenerate_server_key';




    case SERVER_ADD_NAMESPACE = 'server_add_namespace';


    case NAMESPACE_OWNER_CHANGE = 'namespace_owner_change';




    public static function tryFromInput(string|int|bool|null $test): TypeOfEvent
    {
        $maybe = TypeOfEvent::tryFrom($test);
        if (!$maybe) {
            $delimited_values = implode('|', array_column(TypeOfEvent::cases(), 'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum", ['ref' => $test, 'enum_list' => $delimited_values]));
        }
        return $maybe;
    }
}



