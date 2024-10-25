<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class SetChildDescriptionAdd extends Act\Cmd
{
    const UUID = '2761a348-249c-4d08-8fe3-a76936e32148';
    const ACTION_NAME = TypeOfAction::CMD_SET_CHILD_HANDLE_ADD;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

