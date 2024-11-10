<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignPublishPromoteMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class DesignPublishPromote extends Act\Cmd\Ds
{
    const UUID = '7f0faf07-aecc-407e-9b59-dfd683004d62';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_PUBLISH_PROMOTE;

    const ATTRIBUTE_CLASSES = [
        DesignPublishPromoteMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class
    ];

}

