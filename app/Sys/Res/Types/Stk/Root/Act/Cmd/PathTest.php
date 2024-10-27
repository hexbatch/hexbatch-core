<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\PathTestMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class PathTest extends Act\Cmd
{
    const UUID = '617b55ef-8f66-41e1-bfe6-588aa8098d71';
    const ACTION_NAME = TypeOfAction::CMD_PATH_TEST;

    const ATTRIBUTE_CLASSES = [
        PathTestMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

