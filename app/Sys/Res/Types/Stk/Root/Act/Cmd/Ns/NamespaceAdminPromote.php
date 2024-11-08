<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\NamespaceAdminPromoteMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class NamespaceAdminPromote extends Act\Cmd\Ns
{
    const UUID = '8291668d-74fe-45fb-ac44-88681af48eaa';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_ADMIN_PROMOTE;

    const ATTRIBUTE_CLASSES = [
        NamespaceAdminPromoteMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class,
    ];

}

