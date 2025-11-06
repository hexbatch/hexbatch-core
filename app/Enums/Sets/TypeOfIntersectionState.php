<?php
namespace App\Enums\Sets;
use App\Data\ApiParams\Enums\EnumTryTrait;

/**
 * postgres enum type_of_intersection_state
 */

enum TypeOfIntersectionState : string {
    use EnumTryTrait;
  case ENCLOSED = 'enclosed';
  case DISJOINED = 'disjoined';
  case OVERLAPPING = 'overlapping';


}


