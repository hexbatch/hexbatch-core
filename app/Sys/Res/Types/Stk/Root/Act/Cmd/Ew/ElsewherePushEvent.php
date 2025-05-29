<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ew;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


/**
 *
 */
class ElsewherePushEvent extends Act\Cmd\Ew
{
    const UUID = '67d4b737-528b-4747-9b7b-3b4939d01602';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_PUSH_EVENT;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class,
        Act\SystemPrivilege::class,
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ElsewherePushingEvent::class
    ];

}

