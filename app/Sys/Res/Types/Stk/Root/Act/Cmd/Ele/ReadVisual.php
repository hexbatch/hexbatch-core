<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * This also includes the live attributes that can contribute
 */
class ReadVisual extends Act\Cmd\Ele
{
    const UUID = '81524f4a-d0c8-4818-aad7-5845b76148b5';
    const ACTION_NAME = TypeOfAction::PRAGMA_READ_VISUAL;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Pragma::class,
        Act\Cmd\Ele::class,
    ];

    const EVENT_CLASSES = [
        Evt\Element\ReadingVisual::class
    ];

}

