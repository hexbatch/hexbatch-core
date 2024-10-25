<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class SetDestroy extends Act\Cmd
{
    const UUID = 'bb92f8d7-1bdf-4dec-9ba6-d903bfc075c2';
    const ACTION_NAME = TypeOfAction::CMD_SET_DESTROY;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

