<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class SetMemberAdd extends Act\Cmd
{
    const UUID = 'ebd1275e-ecc6-486e-89cb-69e14ae4a44c';
    const ACTION_NAME = TypeOfAction::CMD_SET_MEMBER_ADD;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

