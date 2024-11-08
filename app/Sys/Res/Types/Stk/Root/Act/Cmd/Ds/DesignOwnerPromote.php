<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignOwnerPromoteMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class DesignOwnerPromote extends Act\Cmd\Ds
{
    const UUID = '3feda9e3-e732-41b0-88c6-3d3f45e83bf4';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_OWNER_PROMOTE;

    const ATTRIBUTE_CLASSES = [
        DesignOwnerPromoteMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class,

    ];

}

