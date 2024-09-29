<?php
namespace App\Enums\Paths;
/**
 * postgres enum time_comparison_type
 */
enum TimeComparisonType : string {

  case NO_TIME_COMPARISON = 'no_time_comparison';
  case AGE_ELEMENT = 'age_element';
  case JOINED_SET_AT = 'joined_set_at';
  case AGE_TYPE = 'age_type';
  case ELEMENT_VALUE_CHANGED = 'element_value_changed' ;

}


