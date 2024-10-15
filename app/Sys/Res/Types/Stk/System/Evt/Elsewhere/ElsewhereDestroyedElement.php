<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class ElsewhereDestroyedElement extends Evt\ScopeSet
{
    const UUID = '90134989-7edd-4435-9731-d423c7e9388a';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_DESTROYED_ELEMENT;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeElsewhere::UUID
    ];

}

