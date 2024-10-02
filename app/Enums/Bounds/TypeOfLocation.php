<?php
namespace App\Enums\Bounds;
enum TypeOfLocation : string {
    case MAP = 'map';
    case SHAPE = 'shape';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfLocation {
        $maybe  = TypeOfLocation::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfLocation::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}

