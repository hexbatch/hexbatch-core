<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/*
 * The live type attached is also added to the target(s) with the live type's attribute values
 */
class LiveTypeCopy extends Act\Cmd\Ele
{
    const UUID = '49390d1b-5ed0-49ea-9867-9615c2a1235e';
    const ACTION_NAME = TypeOfAction::CMD_LIVE_TYPE_COPY;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\LiveTypePasted::class
    ];

}

