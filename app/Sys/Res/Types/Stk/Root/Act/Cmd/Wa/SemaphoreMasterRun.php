<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Wa;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * @see MasterSemaphore
 * Runs a published master
 *
 */
class SemaphoreMasterRun extends Act\Cmd\Wa
{
    const UUID = 'd5895d42-9383-4d4d-9e45-ce7d5c0c5580';
    const ACTION_NAME = TypeOfAction::CMD_SEMAPHORE_MASTER_RUN;


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

