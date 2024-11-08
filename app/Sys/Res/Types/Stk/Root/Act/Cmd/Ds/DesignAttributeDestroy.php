<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignAttributeDestroyMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class DesignAttributeDestroy extends Act\Cmd\Ds
{
    const UUID = '079cfc62-0fa2-47f1-84c0-df0fa90441c5';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_ATTRIBUTE_DESTROY;

    const ATTRIBUTE_CLASSES = [
        DesignAttributeDestroyMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

