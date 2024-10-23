<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * there was a phase edit action and the type was removed from one or more places
 */
class TypePhaseRemoved extends Evt\ScopeSet
{
    const UUID = '27aff549-4d9f-47a9-b7f9-769743928b2e';
    const EVENT_NAME = TypeOfEvent::TYPE_PHASE_REMOVED;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeType::UUID
    ];

}

