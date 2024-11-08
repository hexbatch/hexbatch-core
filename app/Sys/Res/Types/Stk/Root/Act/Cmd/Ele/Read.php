<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\ReadMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class Read extends Act\Cmd\Ele
{
    const UUID = '6280f4c3-f2de-49c1-8b4e-5f3e7aab008c';
    const ACTION_NAME = TypeOfAction::PRAGMA_READ;

    const ATTRIBUTE_CLASSES = [
        ReadMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Pragma::class,
        Act\Cmd\Ele::class,
    ];

    const EVENT_CLASSES = [
        Evt\Set\Read::class
    ];

}

