<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Evt;


/*
 * these go to both types, the element and the set this is happening, and child sets this element is inside,
 * because live types apply to element in child sets
 * any getting this event can deny it
 */
class LiveTypeAdded extends Evt\ScopeSet
{
    const UUID = 'e8d7572b-5ca8-4cb0-9bfa-0ffe2a99e8b9';
    const EVENT_NAME = TypeOfEvent::LIVE_TYPE_ADDED;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeSet::UUID
    ];

}

