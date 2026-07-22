<?php
namespace App\Enums\Types;
use App\Data\ApiParams\Enums\EnumTryTrait;

/**
 * postgres enum type_of_server_event_access
 */

enum TypeOfServerEventAccess : string {
    use EnumTryTrait;
  case USE_HANDLER = 'use_handler';
  case FORBIDDEN_EVENT = 'forbidden_event';

}


