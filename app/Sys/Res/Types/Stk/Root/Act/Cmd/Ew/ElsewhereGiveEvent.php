<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ew;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewhereGiveEvent extends Act\Cmd\Ew
{
    const UUID = '5d45cd0c-c8ca-4b02-8887-10f280ee3839';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_GIVE_EVENT;

    const ATTRIBUTE_CLASSES = [
        Metrics\ElsewhereGiveEventMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ElsewhereGivesEvent::class
    ];

}

