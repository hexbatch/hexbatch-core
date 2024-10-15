<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class ElementDestructionBatch extends Evt\ScopeSet
{
    const UUID = '60d62ad8-e20e-49f8-9e9c-0f05c416b43c';
    const EVENT_NAME = TypeOfEvent::ELEMENT_DESTRUCTION_BATCH;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeType::UUID
    ];

}

