<?php
namespace App\Enums\Rules;
/**
 * postgres enum rule_data_action_type
 */
enum RuleDataActionType : string {

  case NO_ACTION = 'no_action';
  case PRAGMA_READ_BOUNDS_TIME = 'pragma_read_bounds_time';
  case PRAGMA_READ_BOUNDS_SHAPE = 'pragma_read_bounds_shape';
  case PRAGMA_READ_BOUNDS_MAP = 'pragma_read_bounds_map';
  case PRAGMA_READ_BOUNDS_PATH = 'pragma_read_bounds_path';
  case READ = 'read';

    public static function tryFromInput(string|int|bool|null $test ) : RuleDataActionType {
        $maybe  = RuleDataActionType::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(RuleDataActionType::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

}


