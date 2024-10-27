<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignTimeTestMetric;
use App\Sys\Res\Types\Stk\Root\Act;

/**
 * Can be given another type to copy the schedule from
 */
class DesignTimeTest extends Act\Cmd
{
    const UUID = 'f6986ecb-de5e-4551-86cf-2cbc855b9780';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_TIME_TEST;

    const ATTRIBUTE_CLASSES = [
        DesignTimeTestMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

