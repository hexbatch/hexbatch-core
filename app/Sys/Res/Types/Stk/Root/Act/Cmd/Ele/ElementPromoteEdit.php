<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\ElementPromoteEditMetric;
use App\Sys\Res\Types\Stk\Root\Act;

/**
 * Edit element ownership or phase  without events or consideration for rules
 *
 */

class ElementPromoteEdit extends Act\Cmd\Ele
{
    const UUID = '384ef934-d5a3-45c0-99d6-80e80adfe631';
    const ACTION_NAME = TypeOfAction::CMD_ELEMENT_PROMOTE_EDIT;

    const ATTRIBUTE_CLASSES = [
        ElementPromoteEditMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class,
    ];

}

