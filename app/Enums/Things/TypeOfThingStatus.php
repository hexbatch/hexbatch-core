<?php
namespace App\Enums\Things;
/**
 * postgres enum type_of_thing_status
 */
enum TypeOfThingStatus : string {

  case PENDING = 'pending';
  case FINISHED_APPROVED = 'finished_approved';
  case FINISHED_DENIED = 'finished_denied';
  case ERROR = 'error';

}


