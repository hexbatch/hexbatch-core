<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Op;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
/*
  * push adds e to the last of the set,the p min and max control how many elements should be processed.
    (p) -> A(+e)
 */

class Push extends Act\Op
{
    const UUID = 'ae5cf895-fee6-4042-93d2-ce83cfa77d05';
    const ACTION_NAME = TypeOfAction::OP_PUSH;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Op::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\SetEnter::class,
    ];

}

