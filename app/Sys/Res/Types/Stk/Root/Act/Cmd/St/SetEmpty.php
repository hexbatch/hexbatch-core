<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\St;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\SetEmptyMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class SetEmpty extends Act\Cmd\St
{
    const UUID = 'b1a1bc7c-a5b2-4cd1-9909-9355c8d38082';
    const ACTION_NAME = TypeOfAction::CMD_SET_EMPTY;

    const ATTRIBUTE_CLASSES = [
        SetEmptyMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\St::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\SetLeave::class,
    ];

}

