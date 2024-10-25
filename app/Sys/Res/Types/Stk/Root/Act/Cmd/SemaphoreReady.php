<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class SemaphoreReady extends Act\Cmd
{
    const UUID = '9f586739-9dc5-4131-9b7b-771c2e194c2f';
    const ACTION_NAME = TypeOfAction::CMD_SEMAPHORE_READY;


    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

