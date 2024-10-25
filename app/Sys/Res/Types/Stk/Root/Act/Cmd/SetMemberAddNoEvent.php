<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class SetMemberAddNoEvent extends Act\Cmd
{
    const UUID = 'ebb85179-85b7-4e31-9ed3-e6be0de8a88f';
    const ACTION_NAME = TypeOfAction::CMD_SET_MEMBER_ADD_NO_EVENT;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

