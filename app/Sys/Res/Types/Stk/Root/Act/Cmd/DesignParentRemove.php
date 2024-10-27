<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignParentRemoveMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class DesignParentRemove extends Act\Cmd
{
    const UUID = 'bf333396-fdcc-45ac-977c-2a9be8f9840c';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_PARENT_REMOVE;

    const ATTRIBUTE_CLASSES = [
        DesignParentRemoveMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

