<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;

/**
 *
 */
class LiveTypePromote extends Act\Cmd\Ele
{
    const UUID = 'a0f933cd-c58b-4499-b378-927827f3d0bb';
    const ACTION_NAME = TypeOfAction::CMD_LIVE_TYPE_PROMOTE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class,
    ];

}

