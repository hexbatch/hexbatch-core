<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\WriteMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


class Write extends Act\Cmd\Ele
{
    const UUID = '51e9a358-c2b1-4876-a518-0ab65d1be224';
    const ACTION_NAME = TypeOfAction::PRAGMA_WRITE;

    const ATTRIBUTE_CLASSES = [
        WriteMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class,
        Act\Pragma::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\AttributeWrite::class
    ];

}

