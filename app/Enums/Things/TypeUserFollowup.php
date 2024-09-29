<?php
namespace App\Enums\Things;
/**
 * postgres enum type_user_followup
 */
enum TypeUserFollowup : string {
    case NOTHING = 'nothing';
  case DIRECT = 'direct';
  case POLLED = 'polled';
  case CALLBACK_SUCCESSFUL = 'callback_successful';
  case CALLBACK_ERROR = 'callback_error';


}


