<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class SetMemberRemove extends Act\Cmd
{
    const UUID = '3cf263d1-3aef-4c96-aed4-01a3c2bd1f98';
    const ACTION_NAME = TypeOfAction::CMD_SET_MEMBER_REMOVE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

