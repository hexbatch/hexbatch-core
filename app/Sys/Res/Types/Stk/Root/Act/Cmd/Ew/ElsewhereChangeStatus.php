<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ew;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\ElsewhereChangeStatusMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewhereChangeStatus extends Act\Cmd\Ew
{
    const UUID = '09a2c919-9f98-4d1c-b438-2132fbc2ff2c';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_CHANGE_STATUS;

    const ATTRIBUTE_CLASSES = [
        ElsewhereChangeStatusMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class,
        Act\SystemPrivilege::class,
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ServerStatusAllowed::class,
        Evt\Elsewhere\ServerStatusBlocked::class,
        Evt\Elsewhere\ServerStatusPaused::class,
        Evt\Elsewhere\ServerStatusPending::class,
    ];

}

