<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Us;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class UserRegister extends Act\Cmd\Us
{
    const UUID = '2cca7cb0-4bde-4b66-ac54-302fba98853e';
    const ACTION_NAME = TypeOfAction::CMD_USER_REGISTER;

    const ATTRIBUTE_CLASSES = [
        Metrics\UserRegisterMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Us::class,
        Act\SystemPrivilege::class,
    ];

    const EVENT_CLASSES = [
        Evt\Server\UserRegistrationProcessing::class
    ];

}

