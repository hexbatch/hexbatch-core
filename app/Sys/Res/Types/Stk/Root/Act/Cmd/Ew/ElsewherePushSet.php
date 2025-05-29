<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ew;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


/**
 *
 */
class ElsewherePushSet extends Act\Cmd\Ew
{
    const UUID = '5b5b878e-1a29-49d5-8524-7fd61627e8a2';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_PUSH_SET;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class,
        Act\SystemPrivilege::class,
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ElsewherePushingSet::class
    ];

}

