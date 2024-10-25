<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * Add a single live rule to the type
 */

class DesignLiveRuleAdd extends Act\Cmd
{
    const UUID = 'd6d8f371-fdc8-4465-89de-35e6c02456a5';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_LIVE_RULE_ADD;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

