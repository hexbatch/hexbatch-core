<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * Fired on the private namespace element when multiple elements from the same type is given to the user
 * if ns assigned when an element is created, the element creation fails for that batch if this rejects
 */
class ElementRecievedBatch extends Evt\ScopeElement
{
    const UUID = '42216ea7-42b2-4bb8-b699-ad365275b54a';
    const EVENT_NAME = TypeOfEvent::ELEMENT_RECIEVED_BATCH;







    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];

}

