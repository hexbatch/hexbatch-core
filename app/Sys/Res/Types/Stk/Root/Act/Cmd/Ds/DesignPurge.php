<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignPurgeMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class DesignPurge extends Act\Cmd
{
    const UUID = '39693e91-d477-4a68-a8ba-7b8a41e94718';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_PURGE;


    const ATTRIBUTE_CLASSES = [
        DesignPurgeMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

