<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignRequirementRemoveMetric;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * Add a single live rule to the type
 */

class DesignRequirementRemove extends Act\Cmd\Ds
{
    const UUID = '3803e692-d952-47d1-8964-e181a0e95233';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_REQUIREMENT_REMOVE;

    const ATTRIBUTE_CLASSES = [
        DesignRequirementRemoveMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

