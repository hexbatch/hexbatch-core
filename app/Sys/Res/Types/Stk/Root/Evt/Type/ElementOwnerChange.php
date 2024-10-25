<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * This goes to the type  when a single element of a type is changed ownership or given for the first time
 */
class ElementOwnerChange extends Evt\ScopeSet
{
    const UUID = 'c43da607-84d1-40f5-992d-db4d091e6ec9';
    const EVENT_NAME = TypeOfEvent::ELEMENT_OWNER_CHANGE;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];

}

