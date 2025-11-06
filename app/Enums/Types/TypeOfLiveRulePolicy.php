<?php
namespace App\Enums\Types;
use App\Data\ApiParams\Enums\EnumTryTrait;

/**
 * postgres enum type_of_live_rule_policy
 */

enum TypeOfLiveRulePolicy : string {
    use EnumTryTrait;
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

}


