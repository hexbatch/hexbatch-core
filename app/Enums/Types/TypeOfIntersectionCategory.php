<?php
namespace App\Enums\Types;
use App\Data\ApiParams\Enums\EnumTryTrait;

/**
 * postgres enum type_of_intersection_category
 */

enum TypeOfIntersectionCategory : string {
    use EnumTryTrait;
  case enclosed = 'enclosed';
  case enclosing = 'enclosing';
  case intersecting = 'intersecting';
  case not_set = 'not_set';

}


