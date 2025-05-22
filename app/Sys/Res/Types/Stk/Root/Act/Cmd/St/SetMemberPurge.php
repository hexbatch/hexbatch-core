<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\St;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;


class SetMemberPurge extends Act\Cmd\St
{
    const UUID = '92b452a7-e0a7-4449-af30-8220f68ab70e';
    const ACTION_NAME = TypeOfAction::CMD_SET_MEMBER_PURGE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\St::class
    ];

}

