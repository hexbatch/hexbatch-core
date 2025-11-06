<?php
namespace App\Enums\Server;
use App\Data\ApiParams\Enums\EnumTryTrait;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;
/**
 * postgres enum type_of_server_status
 */
#[OA\Schema(title: "Server status")]
enum TypeOfServerStatus : string {
    use EnumTryTrait;
  case UNKNOWN_SERVER = 'unknown_server';
  case PENDING_SERVER = 'pending_server';
  case ALLOWED_SERVER = 'allowed_server';
  case PAUSED_SERVER = 'paused_server';
  case BLOCKED_SERVER = 'blocked_server'; //no data exchange


}


