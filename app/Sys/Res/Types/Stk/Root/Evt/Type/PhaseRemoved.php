<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * there was a phase edit action and the type was removed from one or more places
 */
class PhaseRemoved extends Evt\ScopeType
{
    const UUID = '27aff549-4d9f-47a9-b7f9-769743928b2e';
    const EVENT_NAME = TypeOfEvent::PHASE_REMOVED;







    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];

}

