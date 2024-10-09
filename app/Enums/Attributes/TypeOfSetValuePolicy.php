<?php
namespace App\Enums\Attributes;
/**
 * postgres enum type_of_set_value_policy
 */
enum TypeOfSetValuePolicy : string {
    case static = 'static';
    case per_child = 'per_child';
    case per_set = 'per_set';
    case per_all = 'per_all';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfSetValuePolicy {
        $maybe  = TypeOfSetValuePolicy::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfSetValuePolicy::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


