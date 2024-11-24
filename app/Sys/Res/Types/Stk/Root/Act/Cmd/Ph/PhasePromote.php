<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ph;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\PhasePromoteMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class PhasePromote extends Act\Cmd\Ph
{
    const UUID = '24d33a5b-ed63-48f4-b45d-f729734af6ef';
    const ACTION_NAME = TypeOfAction::CMD_PHASE_PROMOTE;

    const ATTRIBUTE_CLASSES = [
        PhasePromoteMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ph::class,
        Act\SystemPrivilege::class
    ];

    const EVENT_CLASSES = [
        Evt\Type\PhaseAdded::class,
    ];

}

