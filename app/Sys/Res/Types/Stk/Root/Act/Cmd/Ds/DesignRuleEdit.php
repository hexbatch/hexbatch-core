<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignRuleEditMetric;
use App\Sys\Res\Types\Stk\Root\Act;

/**
 * Edits a single rule, not a tree
 */
class DesignRuleEdit extends Act\Cmd\Ds
{
    const UUID = 'eab47a5b-a43a-442b-954f-2f621733b48e';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_RULE_EDIT;

    const ATTRIBUTE_CLASSES = [
        DesignRuleEditMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

