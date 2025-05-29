<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ew;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * Server wants another server to have the element we gave them
 */
class ElsewhereDestroyedElement extends Act\Cmd\Ew
{
    const UUID = '4288f35c-f178-44d8-9c47-22214bc7d822';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_DESTROYED_ELEMENT;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ElsewhereDestroyingElement::class
    ];

}

