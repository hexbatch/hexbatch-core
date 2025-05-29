<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Wa;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class SemaphoreReset extends Act\Cmd\Wa
{
    const UUID = '1b178a4d-885e-4dc0-a8f8-caff0d8cd572';
    const ACTION_NAME = TypeOfAction::CMD_SEMAPHORE_RESET;


    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Wa::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\SetEnter::class,
        Evt\Set\SetLeave::class,
    ];

}

