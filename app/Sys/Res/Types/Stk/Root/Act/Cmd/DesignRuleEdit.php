<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class DesignRuleEdit extends Act\Cmd
{
    const UUID = 'eab47a5b-a43a-442b-954f-2f621733b48e';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_RULE_EDIT;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

