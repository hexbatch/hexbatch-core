<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * there was a phase edit action and the type was added into one or more places
 */
class PhaseCutting extends Evt\ScopeType
{
    const UUID = '1d750ad9-81dc-4771-82b4-cea8be3f9458';
    const EVENT_NAME = TypeOfEvent::PHASE_CUTTING;







    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];

}

