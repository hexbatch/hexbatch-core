<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignPromoteMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class DesignPromote extends Act\Cmd\Ds
{
    const UUID = '81b8c1e3-0cf5-409f-b3b9-4fb1a676c9ae';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_PROMOTE;


    const ATTRIBUTE_CLASSES = [
        DesignPromoteMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class,
    ];

}

