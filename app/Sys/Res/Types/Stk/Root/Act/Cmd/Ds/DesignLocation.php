<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignLocationMetric;
use App\Sys\Res\Types\Stk\Root\Act;

/**
 *  Can be given another type to copy the location from
 *
 * /
 */
class DesignLocation extends Act\Cmd
{
    const UUID = '6d695135-cecb-4dc7-8868-0b68f31bb065';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_LOCATION;

    const ATTRIBUTE_CLASSES = [
        DesignLocationMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

