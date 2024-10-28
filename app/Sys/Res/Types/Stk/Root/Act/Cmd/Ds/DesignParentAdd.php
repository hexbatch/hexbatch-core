<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignParentAddMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class DesignParentAdd extends Act\Cmd
{
    const UUID = '362a3cdf-f013-4bc0-afce-315cba179544';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_PARENT_ADD;

    const ATTRIBUTE_CLASSES = [
        DesignParentAddMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

