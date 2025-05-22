<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElementOn extends Act\Cmd\Ele
{
    const UUID = 'bdee0c46-7428-49ed-acc3-e20d96447ca1';
    const ACTION_NAME = TypeOfAction::PRAGMA_ELEMENT_ON;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Pragma::class,
        Act\Cmd\Ele::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\ElementAttributeOn::class
    ];

}

