<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * Send after something goes away if time boundary
 */
class TimeOutAfter extends Evt\ScopeSet
{
    const UUID = '62bcb91b-927d-44e1-a663-7ac9a1a6d5c5';
    const EVENT_NAME = TypeOfEvent::TIME_OUT_AFTER;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeSet::UUID
    ];

}

