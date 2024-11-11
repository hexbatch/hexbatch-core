<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\TypePublishPromoteMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class TypePublishPromote extends Act\Cmd\Ds
{
    const UUID = '7f0faf07-aecc-407e-9b59-dfd683004d62';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_PUBLISH_PROMOTE;

    const ATTRIBUTE_CLASSES = [
        TypePublishPromoteMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class
    ];

}

