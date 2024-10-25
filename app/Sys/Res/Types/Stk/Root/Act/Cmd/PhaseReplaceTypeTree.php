<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class PhaseReplaceTypeTree extends Act\Cmd
{
    const UUID = '1b33ccfb-65ec-4856-9de0-cbae85eaa753';
    const ACTION_NAME = TypeOfAction::CMD_PHASE_REPLACE_TYPE_TREE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

