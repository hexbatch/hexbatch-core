<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Pa;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\PathPartCreateMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class PathPartCreate extends Act\Cmd
{
    const UUID = 'e2fc566d-ba98-4852-9405-9482080b7efe';
    const ACTION_NAME = TypeOfAction::CMD_PATH_PART_CREATE;

    const ATTRIBUTE_CLASSES = [
        PathPartCreateMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Pa::class
    ];

}

