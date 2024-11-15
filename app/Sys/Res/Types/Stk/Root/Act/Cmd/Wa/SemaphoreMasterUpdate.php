<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Wa;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\SemaphoreMasterUpdateMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * @see MasterSemaphore
 * Updates a waiting master to complete it (manual remotes)
 *
 */
class SemaphoreMasterUpdate extends Act\Cmd\Wa
{
    const UUID = '185796a2-b8e7-4041-84bd-33e4bca683b8';
    const ACTION_NAME = TypeOfAction::CMD_SEMAPHORE_MASTER_UPDATE;


    const ATTRIBUTE_CLASSES = [
        SemaphoreMasterUpdateMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Wa::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\SetEnter::class,
        Evt\Set\SetLeave::class,
    ];

}

