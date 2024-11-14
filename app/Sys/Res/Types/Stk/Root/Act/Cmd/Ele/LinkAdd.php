<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\LinkAddMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class LinkAdd extends Act\Cmd\Ele
{
    const UUID = '6eaef3f7-a458-459f-85aa-75d863677101';
    const ACTION_NAME = TypeOfAction::CMD_LINK_ADD;

    const ATTRIBUTE_CLASSES = [
        LinkAddMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\LinkCreated::class
    ];

}

