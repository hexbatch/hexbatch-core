<?php
namespace App\Enums\Rules;
/**
 * postgres enum rule_trigger_action_type
 */
enum RuleTriggerActionType : string {
  case EXISTS = 'exists';
  case NOT_EXIST = 'not_exist';

    public static function tryFromInput(string|int|bool|null $test ) : RuleTriggerActionType {
        $maybe  = RuleTriggerActionType::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(RuleTriggerActionType::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


