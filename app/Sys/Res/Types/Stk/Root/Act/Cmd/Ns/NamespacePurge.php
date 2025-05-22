<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;


class NamespacePurge extends Act\Cmd\Ns
{
    const UUID = '89059226-b860-4d62-9ff9-1d88a4b7037a';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_PURGE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class,
    ];

}

