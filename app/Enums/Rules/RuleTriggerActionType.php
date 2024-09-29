<?php
namespace App\Enums\Rules;
/**
 * postgres enum rule_trigger_action_type
 */
enum RuleTriggerActionType : string {
  case EXISTS = 'exists';
  case NOT_EXIST = 'not_exist';


}


