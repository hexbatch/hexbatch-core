<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignRuleDestroyMetric;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * Remove a single rule or subtree tree from the attribute, rule if a leaf, subtree if this is a root of more
 */

class DesignRuleDestroy extends Act\Cmd
{
    const UUID = '49d036b2-9f53-4fad-afed-b7d628ac060c';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_RULE_DESTROY;

    const ATTRIBUTE_CLASSES = [
        DesignRuleDestroyMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

