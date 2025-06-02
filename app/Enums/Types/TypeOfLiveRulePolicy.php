<?php
namespace App\Enums\Types;
/**
 * postgres enum type_of_live_rule_policy
 */
enum TypeOfLiveRulePolicy : string {

    /*
      '','', '','','','',
                '','',''
     */
  case no_rule = 'no_rule';
  case apply_live = 'apply_live';
  case required_for_entry = 'required_for_entry';
  case blocked_from_entry = 'blocked_from_entry';
  case disable_live_on_entry = 'disable_if_exists_on_entry';
  case enable_live_on_entry = 'enable_if_exists_on_entry';
  case enforce_stack = 'enforce_stack'; #todo what does this do?
  case drop_when_leaving = 'drop_when_leaving';
  case drop_when_leaving_stack = 'drop_when_leaving_stack';


    public static function tryFromInput(string|int|bool|null $test ) : TypeOfLiveRulePolicy {
        $maybe  = TypeOfLiveRulePolicy::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfLiveRulePolicy::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


