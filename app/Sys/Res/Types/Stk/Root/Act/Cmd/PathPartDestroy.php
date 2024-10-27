<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\PathPartDestroyMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class PathPartDestroy extends Act\Cmd
{
    const UUID = '825f87e0-6302-4bb1-a6a4-a0d9261f965c';
    const ACTION_NAME = TypeOfAction::CMD_PATH_PART_DESTROY;

    const ATTRIBUTE_CLASSES = [
        PathPartDestroyMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

