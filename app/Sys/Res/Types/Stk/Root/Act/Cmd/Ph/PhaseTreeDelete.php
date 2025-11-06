<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ph;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


class PhaseTreeDelete extends Act\Cmd\Ph
{
    const UUID = '123ab097-3288-47f2-b270-37697d2b4e38';
    const ACTION_NAME = TypeOfAction::CMD_PHASE_TREE_DELETE;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ph::class
    ];

    const EVENT_CLASSES = [
        Evt\Type\PhaseTreeDeleted::class,
    ];

}

