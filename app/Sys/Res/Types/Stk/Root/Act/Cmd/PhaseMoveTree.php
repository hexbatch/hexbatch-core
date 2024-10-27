<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\PhaseMoveTreeMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class PhaseMoveTree extends Act\Cmd
{
    const UUID = '417eb53e-1615-42c9-9bfc-4349bfb5daa9';
    const ACTION_NAME = TypeOfAction::CMD_PHASE_MOVE_TREE;

    const ATTRIBUTE_CLASSES = [
        PhaseMoveTreeMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

