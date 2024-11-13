<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignAttributeCreateMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


class DesignAttributeCreate extends Act\Cmd\Ds
{
    const UUID = '47661774-8acc-45fb-8c22-77663177e92c';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_ATTRIBUTE_CREATE;

    const ATTRIBUTE_CLASSES = [
        DesignAttributeCreateMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\DesignPending::class
    ];

}

