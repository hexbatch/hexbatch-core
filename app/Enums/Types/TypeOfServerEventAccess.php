<?php
namespace App\Enums\Types;
use OpenApi\Attributes as OA;
/**
 * postgres enum type_of_server_event_access
 */

enum TypeOfServerEventAccess : string {

  case USE_HANDLER = 'use_handler';
  case FORBIDDEN_EVENT = 'forbidden_event';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfServerEventAccess {
        $maybe  = TypeOfServerEventAccess::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfServerEventAccess::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


