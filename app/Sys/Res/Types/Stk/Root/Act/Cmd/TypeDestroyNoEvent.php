<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class TypeDestroyNoEvent extends Act\Cmd
{
    const UUID = 'bd328b7f-1cbb-49e9-a127-451834dd98e6';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_DESTROY_NO_EVENT;


    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class,
        Act\CmdNoEvents::class,
    ];

}

