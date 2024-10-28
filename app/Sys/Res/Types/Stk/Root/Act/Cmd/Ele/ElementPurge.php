<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class ElementPurge extends Act\Cmd
{
    const UUID = 'da1fda45-5a65-4e85-a8f4-86c1b334648f';
    const ACTION_NAME = TypeOfAction::CMD_ELEMENT_PURGE;

    const ATTRIBUTE_CLASSES = [
        ElementPurge::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class
    ];

}

