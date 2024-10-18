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
    //scoped same element

    //in path element
    case SEARCH_RESULTS = 'search_results'; //ns member of element



    //fired only on the ns private element
    case NAMESPACE_MEMBER_ADDING = 'namespace_member_adding';
    case NAMESPACE_ADMIN_ADDING = 'namespace_admin_adding';
    case NAMESPACE_MEMBER_REMOVING = 'namespace_member_removing';
    case NAMESPACE_ADMIN_REMOVING = 'namespace_admin_removing';
    case NAMESPACE_LOGIN = 'namespace_login'; //the user did a login and this is fired on the default namespace private element




    /*
    _.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-._
    "`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'"
     */
    //scoped to same set

    case ATTRIBUTE_READ = 'attribute_read';
    case ATTRIBUTE_WRITE = 'attribute_write';
    case MAP_DISPLAY_WRITE = 'map_display_write';
    case SHAPE_DISPLAY_WRITE = 'shape_display_write';

    case ELEMENT_ATTRIBUTE_OFF = 'element_attribute_off';
    case ELEMENT_ATTRIBUTE_ON = 'element_attribute_on';
    case ELEMENT_TYPE_OFF = 'element_type_turned_off';
    case ELEMENT_TYPE_ON = 'element_type_turned_on';




    case SET_ENTER = 'set_enter';
    case SET_LEAVE = 'set_leave';

    case SET_CHILD_CREATED = 'set_child_created';
    case SET_CHILD_DESTROYED = 'set_child_destroyed';

    case SET_CREATED = 'set_created';

    case SET_DESTROYED = 'set_destroyed';




    case SHAPE_ENTER = 'shape_enter';
    case SHAPE_LEAVE = 'shape_leave';

    case MAP_ENTER = 'map_enter';
    case MAP_LEAVE = 'map_leave';

    case TYPE_SHAPE_ENCLOSED_START = 'type_shape_enclosed_start';
    case TYPE_SHAPE_ENCLOSED_END = 'type_shape_enclosed_end';

    case TYPE_SHAPE_ENCLOSING_START = 'type_shape_enclosing_start';
    case TYPE_SHAPE_ENCLOSING_END = 'type_shape_enclosing_end';

    case TYPE_MAP_ENCLOSED_START = 'type_map_enclosed_start';
    case TYPE_MAP_ENCLOSED_END = 'type_map_enclosed_end';

    case TYPE_MAP_ENCLOSING_START = 'type_map_enclosing_start';
    case TYPE_MAP_ENCLOSING_END = 'type_map_enclosing_end';



    case LIVE_TYPE_ADDED = 'live_type_added';
    case LIVE_TYPE_REMOVED = 'live_type_removed';
    case LIVE_TYPE_PASTED = 'live_type_pasted';
    case TIME_IN_AFTER = 'time_in_after'; //notice after it happened
    case TIME_OUT_AFTER = 'time_out_after';




    /*
  _.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-._
  "`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'"
   */
    //scoped to changes in the type or attribute definition


    case ELEMENT_CREATION = 'element_creation'; //type or type ancestor ns admin, this event has the ns of the element owner, and can reject based on that.
                                                //can access the placeholder for the new element owner ns
    case ELEMENT_CREATION_BATCH = 'element_creation_batch'; //type or type ancestor ns admin, these elements do not have owners yet

    case ELEMENT_DESTRUCTION = 'element_destruction'; //type or type ancestor ns admin
    case ELEMENT_DESTRUCTION_BATCH = 'element_destruction_batch'; //type or type ancestor ns admin


    case ELEMENT_OWNER_CHANGE = 'element_owner_change'; //element given ownership to a ns, can be first time or to a new owner, have access to both ns vis ns placeholders
    case ELEMENT_OWNER_CHANGE_BATCH = 'element_owner_change_batch'; //element given ownership to a ns, can be first time or to a new owner, have access to both ns vis ns placeholders



    /*
    _.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-._
    "`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'"
     */
    //system wide

    case LINK_CREATED = 'link_created';
    case LINK_DESTROYED = 'link_destroyed';
    case LINK_DESCRIPTION_ADDED = 'link_description_added';
    case LINK_DESCRIPTION_REMOVED = 'link_description_removed';

    case TYPE_DESCRIPTION_ADDED = 'type_description_added';
    case TYPE_DESCRIPTION_REMOVED = 'type_description_removed';

    case ATTRIBUTE_DESCRIPTION_ADDED = 'attribute_description_added';
    case ATTRIBUTE_DESCRIPTION_REMOVED = 'attribute_description_removed';

    case PATH_HANDLE_ADDED = 'path_handle_added';
    case PATH_HANDLE_REMOVED = 'path_handle_removed';

    case TYPE_OWNER_CHANGE = 'type_owner_change'; //type given different ownership from what it started as, parents can block

    case TYPE_PUBLISHED = 'type_published'; //covers both parent types and parent attributes: type or type ancestor ns admin
    case TYPE_RETIRED = 'type_retired'; //type or type ancestor ns admin
    case TYPE_SUSPENDED = 'type_suspended'; //only system admin group
    case TYPE_DELETED = 'type_deleted'; //only system admin group




    case NAMESPACE_CREATED = 'namespace_created';
    case NAMESPACE_DESTROYED = 'namespace_destroyed';

    case WAIT_SEMAPHORE = 'wait_semaphore';

    case WAIT_MUTEX = 'wait_mutex';

    case WAIT_ANY = 'wait_any';
    case WAIT_ALL = 'wait_all';

    case WAIT_AVAILABLE = 'wait_available';




    /*
   _.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-._
   "`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'"
    */
    //system wide server stuff


    case SERVER_REGISTERED = 'server_registered';
    case SERVER_STATUS_ALLOWED = 'server_status_allowed';
    case SERVER_STATUS_BLOCKED = 'server_status_blocked';
    case SERVER_STATUS_PENDING = 'server_status_pending';
    case SERVER_STATUS_PAUSED = 'server_status_paused';

    case ELSEWHERE_PUSH_ELEMENT = 'elsewhere_push_element';
    case ELSEWHERE_PUSH_SET = 'elsewhere_push_set';
    case ELSEWHERE_PUSH_TYPE = 'elsewhere_push_type';
    case ELSEWHERE_PUSH_NAMESPACE = 'elsewhere_push_namespace';
    case ELSEWHERE_PUSH_EVENT = 'elsewhere_push_event';

    case ELSEWHERE_GIVES_ELEMENT = 'elsewhere_gives_element';
    case ELSEWHERE_GIVES_SET = 'elsewhere_gives_set';
    case ELSEWHERE_GIVES_EVENT = 'elsewhere_gives_event';
    case ELSEWHERE_GIVES_TYPE = 'elsewhere_gives_type';
    case ELSEWHERE_GIVES_NAMESPACE = 'elsewhere_gives_namespace';
    case ELSEWHERE_CREDENTIALS_NEW = 'elsewhere_credentials_new';
    case ELSEWHERE_CREDENTIALS_BAD = 'elsewhere_credentials_bad';
    case ELSEWHERE_CREDENTIALS_SENDING = 'elsewhere_credentials_sending';
    case ELSEWHERE_CREDENTIALS_ASKING = 'elsewhere_credentials_asking';


    case ELSEWHERE_SUSPENDED_TYPE = 'elsewhere_suspended_type';
    case ELSEWHERE_DESTROYED_ELEMENT = 'elsewhere_destroyed_element';
    case ELSEWHERE_SHARING_ELEMENT = 'elsewhere_sharing_element';
    case ELSEWHERE_ELEMENT_REENTERED = 'elsewhere_element_reentered'; //element with same uuid come back after copied out








    /*
    _.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-._
    "`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'"
     */
    //mixed scope

    case CUSTOM_EVENT = 'custom_event'; //scope depends on the custom event type parent
    case THING_TREE_SET_PERMISSIONS = 'thing_tree_set_permissions';
    //all scopes system admin ns only, after the tree is created
    //
    case THING_TREE_SET_DEBUGGING = 'thing_tree_set_debugging';
    //all scopes system admin ns only, after the tree is created


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



