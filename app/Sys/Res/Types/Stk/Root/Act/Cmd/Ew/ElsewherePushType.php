<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ew;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


/**
 *
 */
class ElsewherePushType extends Act\Cmd\Ew
{
    const UUID = 'e65ec074-b50e-4844-bdf6-1d7bb4526d0b';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_PUSH_TYPE;

    const ATTRIBUTE_CLASSES = [
        Metrics\ElsewherePushTypeMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class,
        Act\SystemPrivilege::class,
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ElsewherePushingType::class
    ];

}

