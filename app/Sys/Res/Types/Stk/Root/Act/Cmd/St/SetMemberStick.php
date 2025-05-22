<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\St;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;


class SetMemberStick extends Act\Cmd\St
{
    const UUID = '3f6b9034-ecdf-4c13-af07-605cd1d8cca2';
    const ACTION_NAME = TypeOfAction::CMD_SET_MEMBER_STICK;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\St::class
    ];

}

