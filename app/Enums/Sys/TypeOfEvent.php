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

    case TYPE_LIVE_ADDED = 'type_live_added';
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

    case SET_TOP_LEVEL_DESTROYED = 'set_top_level_destroyed';
    case SET_TOP_LEVEL_CREATED = 'set_top_level_created';



    case SERVER_ADD_ELEMENT = 'server_add_element';
    case SERVER_ELEMENT_RECIEVED = 'server_element_recieved'; //when the other server gave it to this server, it is fired on this server, and the element was created
    case SERVER_NAMESPACE_RECIEVED = 'server_namespace_recieved'; //same as above, anyone can listen to these as long as element or ns can be found in a path
    case SERVER_ADD_TYPE = 'server_add_type';
    case SERVER_PROCESS_EVENT = 'server_process_event';
    case SERVER_ADD_SET = 'server_add_set';
    case SERVER_REMOVE_ELEMENT = 'server_remove_element';
    case SERVER_REMOVE_TYPE = 'server_remove_type';
    case SERVER_REMOVE_SET = 'server_remove_set';
    case SERVER_RUN_RULES = 'server_run_rules';
    case SERVER_READ = 'server_read'; //when another server is trying to read data here
    case SERVER_WRITE = 'server_write';
    case SERVER_GET_NAMESPACE_TOKEN = 'server_get_namespace_token';
    case SERVER_ADDED_NAMESPACE = 'server_added_namespace';
    case SERVER_NAMESPACE_REGENERATE_KEY = 'server_namespace_regenerate_key';
    case SERVER_REMOVED_NAMESPACE = 'server_removed_namespace';
    case SERVER_CREATED = 'server_created';
    case SERVER_ALLOWED = 'server_allowed';
    case SERVER_REMOVED = 'server_removed';
    case SERVER_PAUSED = 'server_paused';
    case SERVER_REGENERATE_KEY = 'server_regenerate_key';
    case SERVER_REGENERATE_NAMESPACE_KEY = 'server_regenerate_namespace_key';

    //event listeners on the server events can get the other server's response if they listen after


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



