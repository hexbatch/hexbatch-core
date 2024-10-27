<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\SetChildHandleRemoveMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class SetChildHandleRemove extends Act\Cmd
{
    const UUID = 'd3ad6661-a811-4df0-8050-f0882e438cfa';
    const ACTION_NAME = TypeOfAction::CMD_SET_CHILD_HANDLE_REMOVE;

    const ATTRIBUTE_CLASSES = [
        SetChildHandleRemoveMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

