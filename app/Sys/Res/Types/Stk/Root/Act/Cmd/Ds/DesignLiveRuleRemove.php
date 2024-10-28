<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignLiveRuleRemoveMetric;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * Remove a single live rule for the type
 */

class DesignLiveRuleRemove extends Act\Cmd
{
    const UUID = 'b3681a21-fa89-4bcb-9811-ee1f4cfd998a';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_LIVE_RULE_REMOVE;

    const ATTRIBUTE_CLASSES = [
        DesignLiveRuleRemoveMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

