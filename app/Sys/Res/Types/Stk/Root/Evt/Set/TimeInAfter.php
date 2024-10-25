<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * Send after something already existing comes back because of time boundary
 */
class TimeInAfter extends Evt\ScopeSet
{
    const UUID = '944d4867-5ed4-4e13-b6ae-0b3bb67e451a';
    const EVENT_NAME = TypeOfEvent::TIME_IN_AFTER;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

