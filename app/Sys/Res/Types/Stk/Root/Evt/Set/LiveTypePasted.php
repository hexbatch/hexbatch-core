<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Evt;

/*
 * see live type added, same conditions
 */
class LiveTypePasted extends Evt\ScopeSet
{
    const UUID = 'f0c8a651-c5f0-4db1-b9a4-9bc6f56fbaa8';
    const EVENT_NAME = TypeOfEvent::LIVE_TYPE_PASTED;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeSet::UUID
    ];

}

