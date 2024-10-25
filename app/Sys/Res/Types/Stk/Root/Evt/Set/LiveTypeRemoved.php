<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * see live type added, same-ish
 */
class LiveTypeRemoved extends Evt\ScopeSet
{
    const UUID = 'e49b5441-4df9-462a-a0c1-b26cc0bcc93f';
    const EVENT_NAME = TypeOfEvent::LIVE_TYPE_REMOVED;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

