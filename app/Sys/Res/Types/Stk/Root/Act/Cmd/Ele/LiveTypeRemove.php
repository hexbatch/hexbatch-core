<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\LiveTypeRemoveMetric;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * when live type is being removed , this is a set operation,
 * and the live is removed down-set
 * if the down-set has changed live
 * then this is removed before those changes (the changes are applied again after removal)
 * even if up-set removed later
 */

class LiveTypeRemove extends Act\Cmd
{
    const UUID = '17abdda3-294c-4e2b-8cfc-ece90178b097';
    const ACTION_NAME = TypeOfAction::CMD_LIVE_TYPE_REMOVE;

    const ATTRIBUTE_CLASSES = [
        LiveTypeRemoveMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

}

