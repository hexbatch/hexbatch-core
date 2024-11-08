<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignListenerDestroyMetric;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * Destroy the listening event from the attribute
 */
class DesignListenerDestroy extends Act\Cmd\Ds
{
    const UUID = 'a4fc0537-f43e-461e-9be4-0918f2ec0542';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_LISTENER_DESTROY;

    const ATTRIBUTE_CLASSES = [
        DesignListenerDestroyMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

