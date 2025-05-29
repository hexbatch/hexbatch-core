<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ew;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * Server wants another server to have the element we gave them
 */
class ElsewhereSuspendedType extends Act\Cmd\Ew
{
    const UUID = 'c472caed-1d90-497b-863c-57df6cc21968';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_SUSPENDED_TYPE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ElsewhereSuspendingType::class
    ];

}

