<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\St;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;


class SetMemberPromote extends Act\Cmd\St
{
    const UUID = 'ebb85179-85b7-4e31-9ed3-e6be0de8a88f';
    const ACTION_NAME = TypeOfAction::CMD_SET_MEMBER_PROMOTE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\St::class
    ];

}

