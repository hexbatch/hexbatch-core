<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ph;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * Move the tree to another phase
 */
class PhaseMoveTree extends Act\Cmd\Ph
{
    const UUID = '417eb53e-1615-42c9-9bfc-4349bfb5daa9';
    const ACTION_NAME = TypeOfAction::CMD_PHASE_MOVE_TREE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ph::class
    ];

    const EVENT_CLASSES = [
        Evt\Type\PhaseMoving::class,
        Evt\Type\ElementPhaseChangeBatch::class,
    ];

}

