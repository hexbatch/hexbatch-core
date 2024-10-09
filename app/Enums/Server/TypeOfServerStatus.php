<?php
namespace App\Enums\Server;
/**
 * postgres enum type_of_server_status
 */
enum TypeOfServerStatus : string {

  case UNKNOWN_SERVER = 'unknown_server';
  case PENDING_SERVER = 'pending_server';
  case ALLOWED_SERVER = 'allowed_server';
  case PAUSED_SERVER = 'paused_server';
  case BLOCKED_SERVER = 'blocked_server';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfServerStatus {
        $maybe  = TypeOfServerStatus::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfServerStatus::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

}


