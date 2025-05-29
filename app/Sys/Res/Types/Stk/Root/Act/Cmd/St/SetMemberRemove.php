<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\St;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class SetMemberRemove extends Act\Cmd\St
{
    const UUID = '3cf263d1-3aef-4c96-aed4-01a3c2bd1f98';
    const ACTION_NAME = TypeOfAction::CMD_SET_MEMBER_REMOVE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\St::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\SetLeave::class,
        Evt\Set\ShapeLeave::class,
        Evt\Set\MapLeave::class,
        Evt\Set\TypeMapEnclosedEnd::class,
        Evt\Set\TypeMapEnclosingEnd::class,
        Evt\Set\TypeShapeEnclosedEnd::class,
        Evt\Set\TypeShapeEnclosingEnd::class,
    ];

}

