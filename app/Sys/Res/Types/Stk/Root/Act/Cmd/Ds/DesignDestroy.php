<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignDestroyMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class DesignDestroy extends Act\Cmd
{
    const UUID = 'd21d7294-35f8-4938-bff4-3e57ffe95e55';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_DESTROY;


    const ATTRIBUTE_CLASSES = [
        DesignDestroyMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

