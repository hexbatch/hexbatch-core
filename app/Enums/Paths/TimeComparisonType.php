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

    public static function tryFromInput(string|int|bool|null $test ) : TimeComparisonType {
        $maybe  = TimeComparisonType::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TimeComparisonType::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

}


