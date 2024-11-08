<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignRequirementAddMetric;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * Add a single live rule to the type
 */

class DesignRequirementAdd extends Act\Cmd\Ds
{
    const UUID = '90733796-1184-4cac-9661-044f257eadd7';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_REQUIREMENT_ADD;

    const ATTRIBUTE_CLASSES = [
        DesignRequirementAddMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

