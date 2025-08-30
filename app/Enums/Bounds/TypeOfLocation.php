<?php
namespace App\Enums\Bounds;
use OpenApi\Attributes as OA;
/**
 * postgres enum type_of_location
 */
#[OA\Schema(schema: 'TypeOfLocation',title: "Location type")]
enum TypeOfLocation : string {
    case MAP = 'map';
    case SHAPE = 'shape';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfLocation {
        $maybe  = TypeOfLocation::tryFrom(mb_strtolower($test));
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfLocation::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}

