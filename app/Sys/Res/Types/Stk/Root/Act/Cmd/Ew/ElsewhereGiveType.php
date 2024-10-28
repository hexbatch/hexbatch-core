<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ew;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\ElsewhereGiveTypeMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class ElsewhereGiveType extends Act\Cmd
{
    const UUID = '7f676dcb-4cc5-44a3-b89e-90eaeef7056e';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_GIVE_TYPE;

    const ATTRIBUTE_CLASSES = [
        ElsewhereGiveTypeMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class
    ];

}

