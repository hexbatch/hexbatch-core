<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;


class SetPromote extends Act\Cmd\St
{
    const UUID = '2c339c4a-06b9-4eea-b53e-4adfa950d4cd';
    const ACTION_NAME = TypeOfAction::CMD_SET_PROMOTE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\St::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class
    ];


}

