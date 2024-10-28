<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Pa;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\PathEditMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class PathEdit extends Act\Cmd
{
    const UUID = 'c23e75ef-d869-4809-a110-10d4f579b53b';
    const ACTION_NAME = TypeOfAction::CMD_PATH_EDIT;

    const ATTRIBUTE_CLASSES = [
        PathEditMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Pa::class
    ];

}

