<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Time;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


/**
 * Used in the cron job, but can be used in the things too
 */
class TimeSpanRotation extends Act\Cmd\Time
{
    const UUID = '25acff67-c9d9-48ed-b83b-8ec7b5521df0';
    const ACTION_NAME = TypeOfAction::CMD_TIME_SPAN_ROTATION;



    const PARENT_CLASSES = [
        Act\Cmd\Time::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\TimeInAfter::class,
        Evt\Set\TimeOutAfter::class,
    ];

}

