<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class PhaseCutTypeTree extends Act\Cmd
{
    const UUID = '123ab097-3288-47f2-b270-37697d2b4e38';
    const ACTION_NAME = TypeOfAction::CMD_PHASE_CUT_TYPE_TREE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

