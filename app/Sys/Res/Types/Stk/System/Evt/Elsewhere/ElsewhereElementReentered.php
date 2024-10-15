<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class ElsewhereElementReentered extends Evt\ScopeSet
{
    const UUID = 'ba2a7b72-65a0-4948-b201-dba33ef453c1';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_ELEMENT_REENTERED;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeElsewhere::UUID
    ];

}

