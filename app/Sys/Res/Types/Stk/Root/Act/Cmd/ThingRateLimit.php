<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class ThingRateLimit extends Act\Cmd
{
    const UUID = '5be5bb0b-5f44-4365-92cf-a4944900f39c';
    const ACTION_NAME = TypeOfAction::CMD_THING_RATE_LIMIT;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

