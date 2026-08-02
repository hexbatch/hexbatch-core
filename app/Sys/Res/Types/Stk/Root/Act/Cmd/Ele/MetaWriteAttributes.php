<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;



class MetaWriteAttributes extends Act\Cmd\Ele
{
    const UUID = '9930aac1-0a27-4db8-80a3-3f202e3d4924';
    const ACTION_NAME = TypeOfAction::CMD_LIVE_WRITE_META;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\LiveTypeRemoved::class
    ];

}

