<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\TypeOffMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


class TypeOff extends Act\Pragma
{
    const UUID = '2269dcbd-813d-431f-a8d4-c905012c927f';
    const ACTION_NAME = TypeOfAction::PRAGMA_TYPE_OFF;

    const ATTRIBUTE_CLASSES = [
        TypeOffMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class,
        Act\Pragma::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\ElementTypeOff::class,
    ];

}

