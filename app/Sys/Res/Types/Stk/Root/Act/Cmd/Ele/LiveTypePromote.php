<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 *
 */
class LiveTypePromote extends Act\Cmd\Ele
{
    const UUID = 'a0f933cd-c58b-4499-b378-927827f3d0bb';
    const ACTION_NAME = TypeOfAction::CMD_LIVE_TYPE_PROMOTE;

    const ATTRIBUTE_CLASSES = [
        Metrics\LiveTypePromoteMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

    const EVENT_CLASSES = [
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class,
    ];

}

