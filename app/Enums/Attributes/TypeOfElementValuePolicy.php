<?php
namespace App\Enums\Attributes;
use OpenApi\Attributes as OA;

/**
 * postgres enum type_of_element_value_policy
 */
#[OA\Schema(schema: 'TypeOfElementValuePolicy',title: "Element value policy")]
enum TypeOfElementValuePolicy : string {
    case STATIC = 'static';

    case PER_ELEMENT = 'per_element';
    case PER_CHILD = 'per_set_chain';

    case PER_SET = 'per_set';


    public static function tryFromInput(string|int|bool|null $test ) : TypeOfElementValuePolicy {
        $maybe  = TypeOfElementValuePolicy::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfElementValuePolicy::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


