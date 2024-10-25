<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class TypeDestroy extends Act\Cmd
{
    const UUID = '88dc4468-49e3-4949-a545-f7ebe2b0dea0';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_DESTROY;


    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

