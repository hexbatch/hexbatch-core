<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class ThingPageSize extends Act\Cmd
{
    const UUID = 'b1a1bc7c-a5b2-4cd1-9909-9355c8d38082';
    const ACTION_NAME = TypeOfAction::CMD_THING_PAGE_SIZE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

