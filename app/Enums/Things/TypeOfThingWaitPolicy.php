<?php
namespace App\Enums\Things;
/**
 * postgres enum type_of_thing_wait_policy
 */
enum TypeOfThingWaitPolicy : string {
    case wait_all = 'wait_all';
    case wait_one = 'wait_one';



    public static function tryFromInput(string|int|bool|null $test ) : TypeOfThingWaitPolicy {
        $maybe  = TypeOfThingWaitPolicy::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfThingWaitPolicy::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


