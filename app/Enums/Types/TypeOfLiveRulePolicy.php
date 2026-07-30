<?php
namespace App\Enums\Types;
use App\Data\ApiParams\Enums\EnumTryTrait;

/**
 * postgres enum type_of_live_rule_policy
 */

enum TypeOfLiveRulePolicy : string {
    use EnumTryTrait;

  case NO_RULE = 'no_rule';
  case APPLY_LIVE_ON_ENTRY = 'apply_live_on_entry'; //when enters set defined by this element which has this rule
  case REQUIRED_FOR_ENTRY = 'required_for_entry';
  case BLOCKED_FROM_ENTRY = 'blocked_from_entry';
  case DISABLE_LIVE_ON_ENTRY = 'disable_if_exists_on_entry';
  case ENABLE_LIVE_ON_ENTRY = 'enable_if_exists_on_entry';
  case DROP_WHEN_LEAVING = 'drop_when_leaving';
  case DISABLE_WHEN_LEAVING = 'disable_when_leaving';
  case ENABLE_WHEN_LEAVING = 'enable_when_leaving';

}


