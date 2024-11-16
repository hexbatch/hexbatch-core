<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\ElementPromoteMetric;
use App\Sys\Res\Types\Stk\Root\Act;

/**
 * Makes elements without events or consideration for rules
 *
 *
 * it can access a list of sets from a child to create N per set (and put them in the set)
 * it can find a list of owners, and make a copy of the above for each owner
 *
 *  if no set provided, it will put new element(s) in the caller's home set.
 *   Allows for the caller having no home set, because this is also used to bootstrap the system, and the homes may not be made yet
 *
 */

class ElementPromote extends Act\Cmd\Ele
{
    const UUID = 'e9a6494a-bae5-4fcb-9e4a-9aea86a3dfef';
    const ACTION_NAME = TypeOfAction::CMD_ELEMENT_PROMOTE;

    const ATTRIBUTE_CLASSES = [
        ElementPromoteMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class,
    ];

}

