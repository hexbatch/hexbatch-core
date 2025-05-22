<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Op;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;

/*
 (p) => M
    finds all the sets that share elements, (with path restricting the set/element chosen)
     M is a set without events
 */

class Mutual extends Act\Cmd\Op
{
    const UUID = '7d52ebfa-079c-4a2d-9bef-874a473c5220';
    const ACTION_NAME = TypeOfAction::OP_MUTUAL;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Op::class,
        Act\CmdNoSideEffects::class
    ];

}

