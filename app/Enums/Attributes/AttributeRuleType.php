<?php
namespace App\Enums\Attributes;
enum AttributeRuleType : string {

    case INACTIVE = 'inactive';
    case REQUIRED = 'required';
    case SET_MEMBERSHIP_AFFINITY = 'set_membership_affinity';
    case SET_TOGGLE_AFFINITY = 'set_toggle_affinity';
    case ACTION = 'action';

}


