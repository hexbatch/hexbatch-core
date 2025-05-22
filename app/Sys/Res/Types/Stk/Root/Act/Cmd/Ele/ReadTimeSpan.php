<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * Gets the current or next time span of the element's type, its parents and live
 */
class ReadTimeSpan extends Act\Cmd\Ele
{
    const UUID = '7aca447b-968d-455c-8fc9-8c4705f89771';
    const ACTION_NAME = TypeOfAction::PRAGMA_READ_TIME_SPAN;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Pragma::class,
        Act\Cmd\Ele::class
    ];

    const EVENT_CLASSES = [
        Evt\Element\ReadingTime::class
    ];

}

