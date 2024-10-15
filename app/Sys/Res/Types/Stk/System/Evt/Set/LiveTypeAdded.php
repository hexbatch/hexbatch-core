<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


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

