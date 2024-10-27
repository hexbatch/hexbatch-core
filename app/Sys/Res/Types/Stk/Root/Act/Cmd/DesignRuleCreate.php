<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignRuleCreateMetric;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * Add a single rule or a tree to the attribute
 */

class DesignRuleCreate extends Act\Cmd
{
    const UUID = '32fdfc2b-33f6-4149-bec6-77a3dad30f1e';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_RULE_CREATE;

    const ATTRIBUTE_CLASSES = [
        DesignRuleCreateMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

