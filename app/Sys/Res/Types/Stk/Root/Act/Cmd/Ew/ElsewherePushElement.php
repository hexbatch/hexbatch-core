<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ew;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


/**
 *
 */
class ElsewherePushElement extends Act\Cmd\Ew
{
    const UUID = 'f6a6771d-10bb-4ef7-b404-2e892e143bea';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_PUSH_ELEMENT;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class,
        Act\SystemPrivilege::class,
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ElsewherePushingElement::class
    ];

}

