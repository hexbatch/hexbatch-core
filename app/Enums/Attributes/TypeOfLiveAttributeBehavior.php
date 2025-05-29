<?php
namespace App\Enums\Attributes;
/**
 * postgres enum type_of_live_attribute_behavior
 */
enum TypeOfLiveAttributeBehavior : string {

    case NORMAL = 'normal';
    case FILTER = 'filter';
    case BLOCK = 'block';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfLiveAttributeBehavior {
        $maybe  = TypeOfLiveAttributeBehavior::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfLiveAttributeBehavior::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


