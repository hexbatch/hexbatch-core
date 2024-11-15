<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Wa;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\SemaphoreReadyMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class SemaphoreReady extends Act\Cmd\Wa
{
    const UUID = '9f586739-9dc5-4131-9b7b-771c2e194c2f';
    const ACTION_NAME = TypeOfAction::CMD_SEMAPHORE_READY;


    const ATTRIBUTE_CLASSES = [
        SemaphoreReadyMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Wa::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\SetEnter::class,
        Evt\Set\SetLeave::class,
    ];

}

