<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * Remove a single rule or subtree tree from the attribute
 */

class DesignRuleRemove extends Act\Cmd
{
    const UUID = '49d036b2-9f53-4fad-afed-b7d628ac060c';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_RULE_REMOVE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

