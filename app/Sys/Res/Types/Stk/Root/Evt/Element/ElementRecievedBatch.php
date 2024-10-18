<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * Fired on the private namespace element when multiple elements from the same type is given to the user
 * if ns assigned when an element is created, the element creation fails for that batch if this rejects
 */
class ElementRecievedBatch extends Evt\ScopeElement
{
    const UUID = '42216ea7-42b2-4bb8-b699-ad365275b54a';
    const EVENT_NAME = TypeOfEvent::ELEMENT_RECIEVED;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeElement::UUID
    ];

}

