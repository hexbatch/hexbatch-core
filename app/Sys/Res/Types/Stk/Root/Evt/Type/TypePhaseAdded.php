<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * there was a phase edit action and the type was added into one or more places
 */
class TypePhaseAdded extends Evt\ScopeSet
{
    const UUID = '21a14472-a759-4ea2-a193-63109de478ff';
    const EVENT_NAME = TypeOfEvent::TYPE_PHASE_ADDED;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeType::UUID
    ];

}

