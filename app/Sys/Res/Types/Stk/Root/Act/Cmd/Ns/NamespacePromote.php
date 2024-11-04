<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\NamespacePromoteMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class NamespacePromote extends Act\Cmd
{
    const UUID = '13ca0747-b6f5-4756-b89d-480a8968e95f';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_PROMOTE;

    const ATTRIBUTE_CLASSES = [
        NamespacePromoteMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class,
    ];

}

