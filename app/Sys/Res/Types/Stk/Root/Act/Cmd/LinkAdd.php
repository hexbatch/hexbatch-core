<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class LinkAdd extends Act\Cmd
{
    const UUID = '6eaef3f7-a458-459f-85aa-75d863677101';
    const ACTION_NAME = TypeOfAction::CMD_LINK_ADD;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

