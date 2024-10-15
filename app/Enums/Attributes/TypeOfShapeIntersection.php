<?php
namespace App\Enums\Attributes;
/**
 * postgres enum type_of_shape_intersection
 */
enum TypeOfShapeIntersection : string {

    case DESIGN = 'designed_shape';
    case LIVE = 'live_shape';



    public static function tryFromInput(string|int|bool|null $test ) : TypeOfShapeIntersection {
        $maybe  = TypeOfShapeIntersection::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfShapeIntersection::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


