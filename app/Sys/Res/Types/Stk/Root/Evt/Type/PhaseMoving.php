<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * there was a phase edit action and the type was added into one or more places
 */
class PhaseMoving extends Evt\ScopeType
{
    const UUID = '597c6869-e749-4004-b678-5be840d852de';
    const EVENT_NAME = TypeOfEvent::PHASE_MOVING;







    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];

}

