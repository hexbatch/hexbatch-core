<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class TypeSuspend extends Act\Cmd
{
    const UUID = '8a9abfc8-da8b-4309-b50b-e6b0d7af0e5c';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_SUSPEND;


    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

