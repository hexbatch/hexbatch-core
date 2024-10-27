<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\PhasePurgeMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class PhasePurge extends Act\Cmd
{
    const UUID = 'cfd467de-d23d-43ba-97bb-b5b3c7acfd88';
    const ACTION_NAME = TypeOfAction::CMD_PHASE_PURGE;

    const ATTRIBUTE_CLASSES = [
        PhasePurgeMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class,
        Act\CmdNoEvents::class,
    ];

}

