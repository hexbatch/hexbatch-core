<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ew;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class ElsewhereGiveElement extends Act\Cmd\Ew
{
    const UUID = 'b916d43d-6573-4d94-84e7-f634c227af91';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_GIVE_ELEMENT;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ElsewhereGivesElement::class,
        Evt\Elsewhere\ElsewhereElementReentered::class,

    ];

}

