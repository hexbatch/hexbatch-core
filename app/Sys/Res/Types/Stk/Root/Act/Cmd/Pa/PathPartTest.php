<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Pa;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\PathPartTestMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class PathPartTest extends Act\Cmd\Pa
{
    const UUID = '6fe4e299-be8c-4d45-b19a-31ffaebf21ad';
    const ACTION_NAME = TypeOfAction::CMD_PATH_PART_TEST;

    const ATTRIBUTE_CLASSES = [
        PathPartTestMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Pa::class
    ];

}

