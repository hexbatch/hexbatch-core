<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Us;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class UserEdit extends Act\Cmd\Us
{
    const UUID = '0682ab9c-266d-418a-b017-52026da88737';
    const ACTION_NAME = TypeOfAction::CMD_USER_EDIT;

    const ATTRIBUTE_CLASSES = [
        Metrics\UserEditMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Us::class,
        Act\SystemPrivilege::class,
    ];

    const EVENT_CLASSES = [
        Evt\Server\UserEdit::class
    ];

}

