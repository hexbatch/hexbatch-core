<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * there was a phase edit action and the type was added into one or more places
 */
class PhaseReplacing extends Evt\ScopeType
{
    const UUID = '2b7c5093-5aea-4ad2-85af-fa3fe40941a7';
    const EVENT_NAME = TypeOfEvent::PHASE_REPLACING;







    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];

}

