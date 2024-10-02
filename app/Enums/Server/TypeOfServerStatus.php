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

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfChildLogic {
        $maybe  = TypeOfChildLogic::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfChildLogic::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

}


