<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class ThingJsonSize extends Act\Cmd
{
    const UUID = '9bd276fa-e124-4d19-bf28-95f37eb0dcaa';
    const ACTION_NAME = TypeOfAction::CMD_THING_JSON_SIZE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

