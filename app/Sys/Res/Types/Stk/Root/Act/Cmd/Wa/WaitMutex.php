<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Wa;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\WaitMutexMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class WaitMutex extends Act\Cmd\Wa
{
    const UUID = 'ee9be879-1744-4a3b-ac5b-7b73386bf7c9';
    const ACTION_NAME = TypeOfAction::CMD_WAIT_MUTEX;


    const ATTRIBUTE_CLASSES = [
        WaitMutexMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Wa::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\WaitSuccess::class,
        Evt\Server\WaitFail::class,
    ];

}

