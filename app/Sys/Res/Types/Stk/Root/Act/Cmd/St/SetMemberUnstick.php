<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\St;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;


class SetMemberUnstick extends Act\Cmd\St
{
    const UUID = '65055d51-c880-48b3-a5df-2c5316593c81';
    const ACTION_NAME = TypeOfAction::CMD_SET_MEMBER_UNSTICK;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\St::class
    ];

}

