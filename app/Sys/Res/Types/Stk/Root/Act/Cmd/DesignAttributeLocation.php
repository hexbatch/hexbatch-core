<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignAttributeLocationMetric;
use App\Sys\Res\Types\Stk\Root\Act;


/**
 * Can be given another attribute to copy the location from
 *
 */
class DesignAttributeLocation extends Act\Cmd
{
    const UUID = 'f5fb2d65-4f47-4976-803d-8edda67ed43f';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_ATTRIBUTE_LOCATION;

    const ATTRIBUTE_CLASSES = [
        DesignAttributeLocationMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

