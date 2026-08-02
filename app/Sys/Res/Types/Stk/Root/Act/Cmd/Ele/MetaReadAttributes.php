<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;



class MetaReadAttributes extends Act\Cmd\Ele
{
    const UUID = '24fdffba-4b88-4bbc-852b-712130107f14';
    const ACTION_NAME = TypeOfAction::CMD_LIVE_READ_META;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\LiveTypeRemoved::class
    ];

}

