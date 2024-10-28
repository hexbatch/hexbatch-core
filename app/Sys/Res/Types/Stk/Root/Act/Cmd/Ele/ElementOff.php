<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\ElementOffMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElementOff extends Act\Pragma
{
    const UUID = '8d342e23-c7dc-475f-8d0b-26157ac28302';
    const ACTION_NAME = TypeOfAction::PRAGMA_ELEMENT_OFF;

    const ATTRIBUTE_CLASSES = [
        ElementOffMetric::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\ElementAttributeOff::class
    ];

    const PARENT_CLASSES = [
        Act\Pragma::class,
        Act\Cmd\Ele::class
    ];

}

