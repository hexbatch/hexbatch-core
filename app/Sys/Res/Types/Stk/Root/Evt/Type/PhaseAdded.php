<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * there was a phase edit action and the type was added into one or more places
 */
class PhaseAdded extends Evt\ScopeType
{
    const UUID = '21a14472-a759-4ea2-a193-63109de478ff';
    const EVENT_NAME = TypeOfEvent::PHASE_ADDED;







    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];

}

