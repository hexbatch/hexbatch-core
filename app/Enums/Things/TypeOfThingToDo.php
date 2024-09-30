<?php
namespace App\Enums\Things;
/**
 * postgres enum type_of_thing_to_do
 */
enum TypeOfThingToDo : string {
  case NOTHING = 'nothing';

  case ATTRIBUTE_READ = 'attribute_read';
  case ATTRIBUTE_WRITE = 'attribute_write';

  case ATTRIBUTE_TURNED_OFF = 'attribute_turned_off';
  case ATTRIBUTE_TURNED_ON = 'attribute_turned_on';
  case TYPE_ATTRIBUTES_TURNED_OFF = 'type_attributes_turned_off';
  case TYPE_ATTRIBUTES_TURNED_ON = 'type_attributes_turned_on';

  case ELEMENT_CREATION = 'element_creation';
  case ELEMENT_BATCH_CREATION = 'element_batch_creation';
  case ELEMENT_DESTRUCTION = 'element_destruction';

  case GROUP_OPERATION = 'group_operation';


  case REMOTE = 'remote';
  case STACK = 'stack';

  case SEARCH_RESULTS = 'search_results';


  case SET_OPERATION = 'set_operation';
  case SET_ENTER = 'set_enter';
  case SET_LEAVE = 'set_leave';
  case SET_CONTENTS_SHAPE_CHANGED = 'set_contents_shape_changed';
  case SET_TRANSPORT = 'set_transport';
  case SET_KICK = 'set_kick';
  case SET_CREATED = 'set_created';
  case SET_CHILD_CREATED = 'set_child_created';
  case SET_DESTROYED = 'set_destroyed';
  case SET_CHILD_DESTROYED = 'set_child_destroyed';
  case SET_TOP_LEVEL_DESTROYED = 'set_top_level_destroyed';
  case SET_LINK_CREATED = 'set_link_created';
  case SET_LINK_DESTROYED = 'set_link_destroyed';


  case SERVER_ADD_ELEMENT = 'server_add_element';
  case SERVER_ADD_TYPE = 'server_add_type';
  case SERVER_PROCESS_EVENT = 'server_process_event';
  case SERVER_ADD_SET = 'server_add_set';
  case SERVER_REMOVE_ELEMENT = 'server_remove_element';
  case SERVER_REMOVE_TYPE = 'server_remove_type';
  case SERVER_REMOVE_SET = 'server_remove_set';
  case SERVER_RUN_RULES = 'server_run_rules';
  case SERVER_READ = 'server_read';
  case SERVER_WRITE = 'server_write';
  case SERVER_GET_USER_TOKEN = 'server_get_user_token';
  case SERVER_ADD_REMOTE_USER = 'server_add_remote_user';
  case SERVER_USER_REGENERATE_KEY = 'server_user_regenerate_key';
  case SERVER_REMOVE_REMOTE_USER = 'server_remove_remote_user';
  case SERVER_CREATED = 'server_created';
  case SERVER_ALLOWED = 'server_allowed';
  case SERVER_REMOVED = 'server_removed';
  case SERVER_AFTER_REMOVED = 'server_after_removed';
  case SERVER_PAUSED = 'server_paused';
  case SERVER_REGENERATE_KEY = 'server_regenerate_key';

  case SERVER_SENT_CALLBACK_SERVER_REGENERATED_KEY = 'server_sent_callback_server_regenerated_key';
  case SERVER_SENT_CALLBACK_USER_REGENERATED_KEY = 'server_sent_callback_user_regenerated_key';
  case SERVER_SENT_CALLBACK_GOT_USER_TOKEN = 'server_sent_callback_got_user_token';
  case SERVER_SENT_CALLBACK_REMOVED_REMOTE_USER = 'server_sent_callback_removed_remote_user';
  case SERVER_SENT_CALLBACK_CREATE = 'server_sent_callback_create';
  case SERVER_SENT_CALLBACK_DESTROY = 'server_sent_callback_destroy';
  case SERVER_SENT_CALLBACK_RUN_RULES = 'server_sent_callback_run_rules';
  case SERVER_SENT_CALLBACK_READ = 'server_sent_callback_read';
  case SERVER_SENT_CALLBACK_WRITE = 'server_sent_callback_write';
  case SERVER_SENT_CALLBACK_ELEMENT_ADD = 'server_sent_callback_element_add';
  case SERVER_SENT_CALLBACK_ELEMENT_REQUEST = 'server_sent_callback_element_request';
  case SERVER_SENT_CALLBACK_ELEMENT_REMOVE = 'server_sent_callback_element_remove';
  case SERVER_SENT_CALLBACK_SET_ADD = 'server_sent_callback_set_add';
  case SERVER_SENT_CALLBACK_PROCESS_EVENT = 'server_sent_callback_process_event';


  case SERVER_SENT_CALLBACK_ASK_USER_PERMISSION = 'server_sent_callback_ask_user_permission';


  case SHAPE_INTERSECTION_ENTER = 'shape_intersection_enter';
  case SHAPE_INTERSECTION_LEAVE = 'shape_intersection_leave';
  case SHAPE_BORDERING_ATTACHED = 'shape_bordering_attached';
  case SHAPE_BORDERING_SEPERATED = 'shape_bordering_seperated';

  case TYPE_ATTRIBUTE_PARENT_ADD = 'type_attribute_parent_add';
  case TYPE_PARENT_ADD = 'type_parent_add';
  case TYPE_CREATED_BEFORE = 'type_created_before';
  case TYPE_UPDATED_BEFORE = 'type_updated_before';
  case TYPE_CREATED_AFTER = 'type_created_after';
  case TYPE_UPDATED_AFTER = 'type_updated_after';

  case USER_ADD_BEFORE = 'user_add_before';
  case USER_REMOVE_BEFORE = 'user_remove_before';
  case USER_ADD_AFTER = 'user_add_after';
  case USER_REMOVE_AFTER = 'user_remove_after';
  case USER_OWNER_CHANGE = 'user_owner_change';


  case USER_GROUP_MEMBER_ADD = 'user_group_member_add';
  case USER_GROUP_ADMIN_ADD = 'user_group_admin_add';
  case USER_ADMIN_REMOVING_MEMBER = 'user_admin_removing_member';
  case USER_OWNER_REMOVING_ADMIN = 'user_owner_removing_admin';


}


