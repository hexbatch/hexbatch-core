<?php
namespace App\Enums\Sets;
/**
 * postgres enum type_of_intersection_state
 */

enum TypeOfIntersectionState : string {

  case ENCLOSED = 'enclosed';
  case DISJOINED = 'disjoined';
  case OVERLAPPING = 'overlapping';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfIntersectionState {
        $maybe  = TypeOfIntersectionState::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfIntersectionState::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


