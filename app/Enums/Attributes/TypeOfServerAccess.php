<?php
namespace App\Enums\Attributes;
/**
 * postgres enum type_of_server_access
 */
enum TypeOfServerAccess : string {

    case PRIVATE_ATTRIBUTE = 'private_attribute'; //any attribute that has a read event listener must be private
    case PUBLIC_ATTRIBUTE = 'public_attribute';
    case PROTECTED_ATTRIBUTE = 'protected_attribute';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfServerAccess {
        $maybe  = TypeOfServerAccess::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfServerAccess::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


