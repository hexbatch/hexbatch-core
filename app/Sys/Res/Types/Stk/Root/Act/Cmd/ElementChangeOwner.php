<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class ElementChangeOwner extends Act\Cmd
{
    const UUID = '829b1a2d-8ed9-4950-8883-570c3517cfeb';
    const ACTION_NAME = TypeOfAction::CMD_ELEMENT_CHANGE_OWNER;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

