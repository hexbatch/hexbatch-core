<?php
namespace App\Enums\Server;
/**
 * postgres enum type_of_server_status
 */
enum TypeOfServerStatus : string {

  case PENDING = 'pending';
  case ALLOWED = 'allowed';
  case PAUSED = 'paused';
  case BLOCKED = 'blocked';

}


