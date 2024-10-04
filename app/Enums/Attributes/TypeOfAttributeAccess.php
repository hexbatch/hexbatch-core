<?php
namespace App\Enums\Attributes;
enum TypeOfAttributeAccess : string {

    case NORMAL = 'normal';
    case ELEMENT_PRIVATE = 'element_private';
    case ELEMENT_PRIVATE_ADMIN = 'element_private_admin';
    case TYPE_PRIVATE = 'type_private';
    case TYPE_PRIVATE_ADMIN = 'type_private_admin';


    public static function tryFromInput(string|int|bool|null $test ) : TypeOfAttributeAccess {
        $maybe  = TypeOfAttributeAccess::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfAttributeAccess::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


