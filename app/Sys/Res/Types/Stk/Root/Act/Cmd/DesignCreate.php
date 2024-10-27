<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignCreateMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class DesignCreate extends Act\Cmd
{
    const UUID = 'f635c4b8-5903-4688-802c-c0b28f376be0';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_CREATE;


    const ATTRIBUTE_CLASSES = [
        DesignCreateMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

