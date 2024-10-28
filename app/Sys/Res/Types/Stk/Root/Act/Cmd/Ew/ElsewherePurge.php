<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ew;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\ElsewherePurgeMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class ElsewherePurge extends Act\Cmd
{
    const UUID = '71383481-1b7c-433d-9419-2b45152ab503';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_PURGE;

    const ATTRIBUTE_CLASSES = [
        ElsewherePurgeMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class,
    ];

}

