<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\ElementChangeOwnerMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class ElementChangeOwner extends Act\Cmd\Ele
{
    const UUID = '829b1a2d-8ed9-4950-8883-570c3517cfeb';
    const ACTION_NAME = TypeOfAction::CMD_ELEMENT_CHANGE_OWNER;

    const ATTRIBUTE_CLASSES = [
        ElementChangeOwnerMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

    const EVENT_CLASSES = [
        Evt\Type\ElementOwnerChange::class
    ];

}

