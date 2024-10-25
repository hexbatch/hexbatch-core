<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * Fired on the private namespace element when a single element of a type is given to the user
 * if ns assigned when an element is created, the element creation fails if this rejects
 */
class ElementRecieved extends Evt\ScopeElement
{
    const UUID = 'd9475a78-0c0b-46d6-920c-5ebcd2159f7c';
    const EVENT_NAME = TypeOfEvent::ELEMENT_RECIEVED;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];

}

