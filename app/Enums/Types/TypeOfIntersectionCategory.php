<?php
namespace App\Enums\Types;
/**
 * postgres enum type_of_intersection_category
 */
enum TypeOfIntersectionCategory : string {

  case enclosed = 'enclosed';
  case enclosing = 'enclosing';
  case intersecting = 'intersecting';
  case not_set = 'not_set';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfIntersectionCategory {
        $maybe  = TypeOfIntersectionCategory::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfIntersectionCategory::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


