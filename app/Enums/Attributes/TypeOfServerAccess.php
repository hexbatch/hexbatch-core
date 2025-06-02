<?php
namespace App\Enums\Attributes;

/**
 * postgres enum type_of_server_access
 */
enum TypeOfServerAccess : string {

    case IS_PRIVATE = 'is_private';
    case IS_PUBLIC = 'is_public';
    case IS_PUBLIC_DOMAIN = 'is_public_domain';
    case IS_PROTECTED = 'is_protected';

    public static function tryFromInput(string|int|bool|null $test ) : ?TypeOfServerAccess {
        if ($test === null) {return null;}
        $maybe  = TypeOfServerAccess::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfServerAccess::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


