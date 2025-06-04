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
    case BASE_EVENT = 'base_event';
    case EVENT_SCOPE_ELEMENT = 'event_scope_element';
    case EVENT_SCOPE_ELSEWHERE = 'event_scope_elsewhere';
    case EVENT_SCOPE_SET = 'event_scope_set';
    case EVENT_SCOPE_TYPE = 'event_scope_type';
    case EVENT_SCOPE_SERVER = 'event_scope_server';



    /*
    _.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-._
    "`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'"
     */
    //scoped same element

    //in path element
    case SEARCH_RESULTS = 'search_results'; //goes to handle



    //fired only on the ns private element
    case NAMESPACE_MEMBER_ADDING = 'namespace_member_adding';
    case NAMESPACE_ADMIN_ADDING = 'namespace_admin_adding';
    case NAMESPACE_MEMBER_REMOVING = 'namespace_member_removing';
    case NAMESPACE_ADMIN_REMOVING = 'namespace_admin_removing';
    case NAMESPACE_LOGIN = 'namespace_login'; //the user did a login and this is fired on the default namespace private element


    case ELEMENT_RECIEVED = 'element_recieved';
    case ELEMENT_RECIEVED_BATCH = 'element_recieved_batch';

    /*
    _.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-._
    "`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'"
     */
    //scoped to same set

    case ATTRIBUTE_READING = 'attribute_reading';
    case ATTRIBUTE_WRITE = 'attribute_write';

    case DISPLAY_READING = 'display_reading';
    case TIME_READING = 'time_reading';

    case ELEMENT_TYPE_TURNING_OFF = 'element_type_turning_off';
    case ELEMENT_TYPE_TURNED_OFF = 'element_type_turned_off';
    case ELEMENT_TYPE_TURNING_ON = 'element_type_turning_on';
    case ELEMENT_TYPE_TURNED_ON = 'element_type_turned_on';




    case SET_ENTER = 'set_enter';
    case SET_LEAVE = 'set_leave';

    case SET_CHILD_CREATED = 'set_child_created';
    case SET_CHILD_DESTROYED = 'set_child_destroyed';

    case SET_CREATED = 'set_created';

    case SET_DESTROYED = 'set_destroyed';
    case SET_DESTROYING = 'set_destroying';




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

    case ELEMENT_DESTRUCTION = 'element_destruction'; //type or type ancestor ns admin
    case ELEMENT_DESTROYED = 'element_destroyed'; //after the face


    case ELEMENT_OWNER_CHANGE = 'element_owner_change'; //element given ownership to a ns, can be first time or to a new owner, have access to both ns vis ns placeholders

    case PHASE_REMOVED = 'phase_removed';
    case PHASE_ADDED = 'phase_added';
    case PHASE_MOVING = 'phase_moving';
    case PHASE_CUTTING = 'phase_cutting';
    case PHASE_REPLACING = 'phase_replacing';
    case ELEMENT_PHASE_CHANGE_BATCH = 'element_phase_change_batch';

    /*
    _.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-._
    "`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'"
     */
    //system wide

    case SERVER_EDITED = 'server_edited';
    case LINK_CREATED = 'link_created';
    case LINK_CREATING = 'link_creating';
    case LINK_DESTROYING = 'link_destroying';
    case LINK_DESTROYED = 'link_destroyed';


    case TYPE_HANDLE_ADDED = 'type_handle_added';
    case TYPE_HANDLE_REMOVED = 'type_handle_removed';

    case ATTRIBUTE_HANDLE_ADDED = 'attribute_handle_added';
    case ATTRIBUTE_HANDLE_REMOVED = 'attribute_handle_removed';

    case PATH_HANDLE_ADDED = 'path_handle_added';
    case PATH_HANDLE_REMOVED = 'path_handle_removed';

    case TYPE_OWNER_CHANGING = 'type_owner_changing'; //type given different ownership from what it started as, parents can block
    case TYPE_OWNER_CHANGED = 'type_owner_changed'; //after type given different ownership

    case DESIGN_PENDING = 'design_pending'; //when a design uses a base attribute or parent type. Goes to all listeners in the inheritance chain
    case TYPE_PUBLISHED = 'type_published'; //covers both parent types and parent attributes: type or type ancestor ns admin
    case TYPE_RETIRED = 'type_retired'; //type or type ancestor ns admin
    case TYPE_SUSPENDED = 'type_suspended'; //only system admin group
    case TYPE_DELETED = 'type_deleted'; //only system admin group




    case USER_DELETION_PREPARING = 'user_deletion_preparing';
    case USER_DELETION_STARTING = 'user_deletion_starting';
    case USER_REGISTRATION_STARTING = 'user_registration_starting';
    case USER_REGISTRATION_PROCESSING = 'user_registration_processing';
    case USER_LOGGING_IN = 'user_logging_in';
    case USER_EDIT = 'user_edit';


    case NAMESPACE_CREATED = 'namespace_created';
    case NAMESPACE_STARTING_TRANSFER = 'namespace_starting_transfer';
    case NAMESPACE_TRANSFERRED = 'namespace_transferred';
    case NAMESPACE_DESTROYED = 'namespace_destroyed';

    case NAMESPACE_HANDLE_ADDED = 'namespace_handle_added';
    case NAMESPACE_HANDLE_REMOVED = 'namespace_handle_removed';

    case WAIT_FAIL = 'wait_fail';
    case WAIT_SUCCESS = 'wait_success';



    case CUSTOM_EVENT_FIRED = 'custom_event_fired'; //scope of the event after that depends on the custom event type parent

    /*
   _.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-.__.--.__.-'""`-._
   "`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'""`--'""`-.__.-'"
    */
    //system-wide server stuff


    case SERVER_REGISTERED = 'server_registered';
    case SERVER_STATUS_ALLOWED = 'server_status_allowed';
    case SERVER_STATUS_BLOCKED = 'server_status_blocked';
    case SERVER_STATUS_PENDING = 'server_status_pending';
    case SERVER_STATUS_PAUSED = 'server_status_paused';

    case ELSEWHERE_ASKING_SET = 'elsewhere_asking_set';
    case ELSEWHERE_ASKING_NAMESPACE = 'elsewhere_asking_namespace';
    case ELSEWHERE_ASKING_TYPE = 'elsewhere_asking_type';
    case ELSEWHERE_ASKING_ELEMENT = 'elsewhere_asking_element';
    case ELSEWHERE_PUSHING_ELEMENT = 'elsewhere_pushing_element';
    case ELSEWHERE_PUSHING_SET = 'elsewhere_pushing_set';
    case ELSEWHERE_PUSHING_TYPE = 'elsewhere_pushing_type';
    case ELSEWHERE_PUSHING_NAMESPACE = 'elsewhere_pushing_namespace';
    case ELSEWHERE_PUSHING_EVENT = 'elsewhere_pushing_event';

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



