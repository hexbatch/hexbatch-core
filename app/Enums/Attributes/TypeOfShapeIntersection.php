<?php
namespace App\Enums\Attributes;
enum TypeOfShapeIntersection : string {

    case DESIGN = 'design';
    case LIVE = 'live';



    public static function tryFromInput(string|int|bool|null $test ) : TypeOfShapeIntersection {
        $maybe  = TypeOfShapeIntersection::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfShapeIntersection::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


