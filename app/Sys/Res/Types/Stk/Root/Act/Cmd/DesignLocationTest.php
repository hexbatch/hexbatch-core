<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignLocationTestMetric;
use App\Sys\Res\Types\Stk\Root\Act;

/**
 *  Test the location of the test with other types, attributes or geo-json
 *
 * /
 */
class DesignLocationTest extends Act\Cmd
{
    const UUID = 'f26dcdcb-09e4-41df-b435-3e7b106c6282';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_LOCATION_TEST;

    const ATTRIBUTE_CLASSES = [
        DesignLocationTestMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

