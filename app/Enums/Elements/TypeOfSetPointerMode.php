<?php
namespace App\Enums\Elements;
/**
 * postgres enum type_of_server_status
 */
enum TypeOfSetPointerMode : string {

  case LINK_TO_SET = 'link_to_set';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfSetPointerMode {
        $maybe  = TypeOfSetPointerMode::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfSetPointerMode::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


