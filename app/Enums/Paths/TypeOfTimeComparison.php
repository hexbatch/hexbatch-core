<?php
namespace App\Enums\Paths;
use App\Data\ApiParams\Enums\EnumTryTrait;

/**
 * postgres enum type_of_time_comparison
 */

enum TypeOfTimeComparison : string {
    use EnumTryTrait;
  case NO_TIME_COMPARISON = 'no_time_comparison';
  case AGE_ELEMENT = 'age_element';
  case JOINED_SET_AT = 'joined_set_at';
  case AGE_TYPE = 'age_type';
  case ELEMENT_VALUE_CHANGED = 'element_value_changed' ;


}


