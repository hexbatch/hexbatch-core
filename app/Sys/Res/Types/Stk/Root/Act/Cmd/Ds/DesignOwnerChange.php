<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignOwnerChangeMetric;
use App\Sys\Res\Types\Stk\Root\Act;


/**
 * Change owner of a non-published type, no events raised
 *
 */
class DesignOwnerChange extends Act\Cmd
{
    const UUID = '3baa3285-5dff-42b5-bd22-071ad39101db';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_OWNER_CHANGE;

    const ATTRIBUTE_CLASSES = [
        DesignOwnerChangeMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

