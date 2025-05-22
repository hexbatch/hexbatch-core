<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;

/*
 * Add a single rule or a tree to the attribute, can be inserted into existing tree location or at top
 */

class DesignRuleCreate extends Act\Cmd\Ds
{
    const UUID = '32fdfc2b-33f6-4149-bec6-77a3dad30f1e';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_RULE_CREATE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

