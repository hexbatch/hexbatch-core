<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * This goes to the type  when many elements of the same type are changed ownership or given for the first time.
 * Ancestor can handle for down-type
 */
class ElementOwnerChangeBatch extends Evt\ScopeSet
{
    const UUID = '5e6e6598-9031-466f-a464-589a370d106a';
    const EVENT_NAME = TypeOfEvent::ELEMENT_OWNER_CHANGE_BATCH;







    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];

}

