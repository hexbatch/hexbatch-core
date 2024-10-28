<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Pa;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\PathDestroyMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class PathDestroy extends Act\Cmd
{
    const UUID = '88966fa4-f43f-4f8f-99ad-eeeb2ddf4514';
    const ACTION_NAME = TypeOfAction::CMD_PATH_DESTROY;

    const ATTRIBUTE_CLASSES = [
        PathDestroyMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Pa::class
    ];

}

