<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;


class NamespaceAdminPurge extends Act\Cmd\Ns
{
    const UUID = '2812f86f-ff40-4617-bc51-6ee5184492c3';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_ADMIN_PURGE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class,
    ];

}

