<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * One or more elements were moved from a different phase
 */
class ElementPhaseChangeBatch extends Evt\ScopeSet
{
    const UUID = '08545753-2746-4a80-979e-1b15f3eaab4e';
    const EVENT_NAME = TypeOfEvent::ELEMENT_PHASE_CHANGE_BATCH;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeType::UUID
    ];

}

