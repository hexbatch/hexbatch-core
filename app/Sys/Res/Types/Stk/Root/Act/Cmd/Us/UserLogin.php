<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Us;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class UserLogin extends Act\Cmd\Us
{
    const UUID = '671c5b71-847a-47b1-b34b-f32fc17ee024';
    const ACTION_NAME = TypeOfAction::CMD_USER_LOGIN;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Us::class,
        Act\SystemPrivilege::class,
    ];

    const EVENT_CLASSES = [
        Evt\Server\UserLoggingIn::class
    ];

}

