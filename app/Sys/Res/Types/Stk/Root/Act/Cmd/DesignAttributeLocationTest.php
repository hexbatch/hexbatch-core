<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignAttributeLocationTestMetric;
use App\Sys\Res\Types\Stk\Root\Act;


/**
 * Can be tested against another type, attribute or geo-jason
 *
 */
class DesignAttributeLocationTest extends Act\Cmd
{
    const UUID = '1f104a48-34f4-4338-9723-a62fccbbe83a';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_ATTRIBUTE_LOCATION_TEST;

    const ATTRIBUTE_CLASSES = [
        DesignAttributeLocationTestMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

