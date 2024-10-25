<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class ElementDestroyNoEvent extends Act\Cmd
{
    const UUID = 'da1fda45-5a65-4e85-a8f4-86c1b334648f';
    const ACTION_NAME = TypeOfAction::CMD_ELEMENT_DESTROY_NO_EVENT;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class,
        Act\CmdNoEvents::class,
    ];

}

