<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignListenerCreateMetric;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * Create a listening event to the attribute
 */

class DesignListenerCreate extends Act\Cmd
{
    const UUID = 'dbc2ab51-47e0-4ffa-b009-3a4cfc834485';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_LISTENER_CREATE;

    const ATTRIBUTE_CLASSES = [
        DesignListenerCreateMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

