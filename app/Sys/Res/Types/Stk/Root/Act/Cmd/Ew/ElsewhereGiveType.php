<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ew;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewhereGiveType extends Act\Cmd\Ew
{
    const UUID = '7f676dcb-4cc5-44a3-b89e-90eaeef7056e';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_GIVE_TYPE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ElsewhereGivesType::class
    ];

}

