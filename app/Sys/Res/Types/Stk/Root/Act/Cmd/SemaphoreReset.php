<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class SemaphoreReset extends Act\Cmd
{
    const UUID = '1b178a4d-885e-4dc0-a8f8-caff0d8cd572';
    const ACTION_NAME = TypeOfAction::CMD_SEMAPHORE_RESET;


    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

