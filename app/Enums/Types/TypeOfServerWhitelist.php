<?php
namespace App\Enums\Types;
/**
 * postgres enum type_of_server_status
 */
enum TypeOfServerWhitelist : string {

  case access = 'access';
  case CREATE_ELEMENTS = 'create_elements';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfChildLogic {
        $maybe  = TypeOfChildLogic::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfChildLogic::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


