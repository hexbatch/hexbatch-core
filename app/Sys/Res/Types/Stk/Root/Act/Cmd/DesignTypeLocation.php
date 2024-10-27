<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignTypeLocationMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class DesignTypeLocation extends Act\Cmd
{
    const UUID = '6d695135-cecb-4dc7-8868-0b68f31bb065';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_TYPE_LOCATION;

    const ATTRIBUTE_CLASSES = [
        DesignTypeLocationMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

