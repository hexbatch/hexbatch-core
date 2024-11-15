<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Wa;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\SemaphoreResetMetric;
use App\Sys\Res\Atr\Stk\Act\Metrics\WaitAllMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class WaitAny extends Act\Cmd\Wa
{
    const UUID = 'b5bcce0c-2269-478a-94de-6e568430d724';
    const ACTION_NAME = TypeOfAction::CMD_WAIT_ANY;


    const ATTRIBUTE_CLASSES = [
        WaitAllMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Wa::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\WaitSuccess::class,
        Evt\Server\WaitFail::class,
    ];

}

