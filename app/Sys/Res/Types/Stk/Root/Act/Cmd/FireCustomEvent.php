<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class FireCustomEvent extends Act\Cmd
{
    const UUID = 'ba763bab-9cec-4e03-b9b4-7004381250f0';
    const ACTION_NAME = TypeOfAction::CMD_FIRE_CUSTOM_EVENT;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

