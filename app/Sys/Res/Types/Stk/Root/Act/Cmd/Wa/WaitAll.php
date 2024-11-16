<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Wa;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\WaitAllMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class WaitAll extends Act\Cmd\Wa
{
    const UUID = '0ede94fe-be1e-4d28-b17c-822c6281a6b8';
    const ACTION_NAME = TypeOfAction::CMD_WAIT_ALL;


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

