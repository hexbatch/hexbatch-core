<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * Add a single live rule to the type
 */

class DesignRequirementAdd extends Act\Cmd
{
    const UUID = '90733796-1184-4cac-9661-044f257eadd7';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_REQUIREMENT_ADD;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

