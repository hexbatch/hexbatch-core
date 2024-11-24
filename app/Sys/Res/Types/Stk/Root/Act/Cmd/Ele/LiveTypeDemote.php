<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics;
use App\Sys\Res\Types\Stk\Root\Act;

/**
 *
 */
class LiveTypeDemote extends Act\Cmd\Ele
{
    const UUID = '8292caec-2d1e-4afa-98f4-01ba2654401d';
    const ACTION_NAME = TypeOfAction::CMD_LIVE_TYPE_DEMOTE;

    const ATTRIBUTE_CLASSES = [
        Metrics\LiveTypeDemoteMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class,
    ];


}

