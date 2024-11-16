<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Wa;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\WaitAvailableMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class WaitAvailable extends Act\Cmd\Wa
{
    const UUID = '1aaebe92-7a2f-439e-8eb0-6bd7cf38ba9d';
    const ACTION_NAME = TypeOfAction::CMD_WAIT_AVAILABLE;


    const ATTRIBUTE_CLASSES = [
        WaitAvailableMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Wa::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\WaitSuccess::class,
        Evt\Server\WaitFail::class,
    ];

}

