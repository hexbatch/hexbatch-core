<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Pragma;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


class TypeOn extends Act\Pragma
{
    const UUID = '2d0a931a-be5a-4cab-b177-c9e9ec78e432';
    const ACTION_NAME = TypeOfAction::PRAGMA_TYPE_ON;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Pragma::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\ElementTypeOn::class,
    ];

}

