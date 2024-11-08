<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ph;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\PhaseReplaceTreeMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class PhaseReplaceTree extends Act\Cmd\Ph
{
    const UUID = '1b33ccfb-65ec-4856-9de0-cbae85eaa753';
    const ACTION_NAME = TypeOfAction::CMD_PHASE_REPLACE_TREE;

    const ATTRIBUTE_CLASSES = [
        PhaseReplaceTreeMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ph::class
    ];

}

