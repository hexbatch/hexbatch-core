<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class TypeSuspend extends Act\Cmd\Ty
{
    const UUID = '8a9abfc8-da8b-4309-b50b-e6b0d7af0e5c';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_SUSPEND;


    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ty::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\TypeSuspended::class
    ];

}

