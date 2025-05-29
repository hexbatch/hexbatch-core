<?php

namespace App\Enums\Sys;



enum TypeOfFlag: string
{

    case CAN_WRITE = 'can_write';
    case CAN_READ = 'can_read';
    public static function tryFromInput(string|int|bool|null $test ) : ?TypeOfFlag {
        if ($test === null) {return null;}
        $maybe  = TypeOfFlag::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfFlag::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

}
