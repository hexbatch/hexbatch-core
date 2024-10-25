<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Op;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/*
   * unshift adds e to the first of the set, the p min and max control how many elements should be processed.
    (p) -> A(-e)
when an element is removed from its last set, it is automatically destroyed
 */

class Unshift extends Act\Op
{
    const UUID = 'c4f79042-3be1-4c9a-9342-235341d5f0d0';
    const ACTION_NAME = TypeOfAction::OP_UNSHIFT;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Op::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\SetEnter::class,
    ];

}

