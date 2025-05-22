<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/*
 * when live type is being added , this is a set operation,
 * and the live is applied down-set
 * if the down-set has already applied to a live,
 * then this is applied before those changes (so make the down-set able to mask and filter the upset,
 * even if upset changed later
 *
 * down-set can apply the same type again to reset the values, but that is not seen up-set
 * down-set applying live does not affect up-set
 *
 * when child set destroyed, live attributes go away if the same element above does not have that live applied or is masked by another live
 *
 * live types can mask events to attributes they cover up, if they handle the same event
 *  if the live has a more derived attribute, then it masks the parent attribute rules
 *  if the live has a less derived attribute, then it will filter the attribute it is an ancestor of
 *   the filter chain order is the applied live order
 */
class LiveTypeAdd extends Act\Cmd\Ele
{
    const UUID = '06ffa538-2d8d-460b-922e-e04efe73194e';
    const ACTION_NAME = TypeOfAction::CMD_LIVE_TYPE_ADD;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\LiveTypeAdded::class
    ];

}

