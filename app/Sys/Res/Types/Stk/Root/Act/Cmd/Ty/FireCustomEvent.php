<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\FireCustomEventMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class FireCustomEvent extends Act\Cmd\Ty
{
    const UUID = 'ba763bab-9cec-4e03-b9b4-7004381250f0';
    const ACTION_NAME = TypeOfAction::CMD_FIRE_CUSTOM_EVENT;

    const ATTRIBUTE_CLASSES = [
        FireCustomEventMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ty::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\CustomEventFired::class
    ];

}

