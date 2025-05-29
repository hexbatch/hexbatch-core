<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * Send after something goes away if time boundary
 */
class TimeOutAfter extends Evt\ScopeSet
{
    const UUID = '62bcb91b-927d-44e1-a663-7ac9a1a6d5c5';
    const EVENT_NAME = TypeOfEvent::TIME_OUT_AFTER;







    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

