<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/*
 * see live type added, same conditions
 */
class LiveTypePasted extends Evt\ScopeSet
{
    const UUID = 'f0c8a651-c5f0-4db1-b9a4-9bc6f56fbaa8';
    const EVENT_NAME = TypeOfEvent::LIVE_TYPE_PASTED;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

