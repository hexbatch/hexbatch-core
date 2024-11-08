<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\St;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\SetCreateMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt\Type\ElementSetCreated;


class SetCreate extends Act\Cmd\St
{
    const UUID = '06c6d184-1230-4bd1-9ee4-80657a9e3620';
    const ACTION_NAME = TypeOfAction::CMD_SET_CREATE;

    const ATTRIBUTE_CLASSES = [
        SetCreateMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\St::class
    ];

    const EVENT_CLASSES = [
        ElementSetCreated::class
    ];

}

