<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\NamespaceMemberPromoteMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class NamespaceMemberPromote extends Act\Cmd\Ns
{
    const UUID = '91ff937c-240d-44cb-ab0e-247948e17d07';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_MEMBER_PROMOTE;

    const ATTRIBUTE_CLASSES = [
        NamespaceMemberPromoteMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class,
    ];

}

