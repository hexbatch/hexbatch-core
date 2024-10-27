<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\SemaphoreMasterRunMetric;
use App\Sys\Res\Types\Stk\Root\Act;

/**
 * @see MasterSemaphore
 * Runs a published master
 *
 */
class SemaphoreMasterRun extends Act\Cmd
{
    const UUID = 'd5895d42-9383-4d4d-9e45-ce7d5c0c5580';
    const ACTION_NAME = TypeOfAction::CMD_SEMAPHORE_MASTER_RUN;


    const ATTRIBUTE_CLASSES = [
        SemaphoreMasterRunMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

