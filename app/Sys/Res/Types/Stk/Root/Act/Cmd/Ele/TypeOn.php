<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\TypeOnMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


class TypeOn extends Act\Cmd\Ele
{
    const UUID = '2d0a931a-be5a-4cab-b177-c9e9ec78e432';
    const ACTION_NAME = TypeOfAction::PRAGMA_TYPE_ON;

    const ATTRIBUTE_CLASSES = [
        TypeOnMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class,
        Act\Pragma::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\ElementTypeOn::class,
    ];

}

