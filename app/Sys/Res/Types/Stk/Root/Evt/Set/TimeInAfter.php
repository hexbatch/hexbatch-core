<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * Send after something already existing comes back because of time boundary
 */
class TimeInAfter extends Evt\ScopeSet
{
    const UUID = '944d4867-5ed4-4e13-b6ae-0b3bb67e451a';
    const EVENT_NAME = TypeOfEvent::ELEMENT_ATTRIBUTE_OFF;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeSet::UUID
    ];

}

