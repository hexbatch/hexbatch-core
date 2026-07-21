<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ph;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


class PhaseTreeCopy extends Act\Cmd\Ph
{
    const UUID = '005c55ce-547a-426c-bc97-c2115b9b8789';
    const ACTION_NAME = TypeOfAction::CMD_PHASE_TREE_COPY;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ph::class
    ];

    const EVENT_CLASSES = [
        Evt\Type\PhaseTreeCopied::class,
    ];

}

