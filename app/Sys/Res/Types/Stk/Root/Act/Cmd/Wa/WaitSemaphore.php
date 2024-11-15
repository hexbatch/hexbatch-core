<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Wa;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\SemaphoreResetMetric;
use App\Sys\Res\Atr\Stk\Act\Metrics\WaitSemaphoreMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class WaitSemaphore extends Act\Cmd\Wa
{
    const UUID = '1878e405-7966-4aaf-b574-35efcf203b43';
    const ACTION_NAME = TypeOfAction::CMD_WAIT_SEMAPHORE;


    const ATTRIBUTE_CLASSES = [
        WaitSemaphoreMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Wa::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\WaitSuccess::class,
        Evt\Server\WaitFail::class,
    ];

}

