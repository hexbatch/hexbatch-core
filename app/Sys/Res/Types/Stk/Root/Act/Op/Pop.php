<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Op;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\OpPopMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
/*
  * pop
    (p)A => B(+e) + A(-e)
     removes last to be added, with p doing different ordering, the p min and max control how many elements should be processed. B can be null.
     P can be null, only provide min and or max, or select the elements in which order will be popped
when an element is removed from its last set, it is automatically destroyed
 */
class Pop extends Act\Op
{
    const UUID = '6c46ce70-59cc-4df5-84fc-2e281eb26ee4';
    const ACTION_NAME = TypeOfAction::OP_POP;


    const ATTRIBUTE_CLASSES = [
        OpPopMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Op::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\SetLeave::class,
    ];

}

