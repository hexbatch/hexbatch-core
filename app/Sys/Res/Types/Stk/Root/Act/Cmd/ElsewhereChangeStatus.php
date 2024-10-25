<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class ElsewhereChangeStatus extends Act\Cmd
{
    const UUID = '09a2c919-9f98-4d1c-b438-2132fbc2ff2c';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_CHANGE_STATUS;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

