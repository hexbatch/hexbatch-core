<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class ElementCreation extends Evt\ScopeSet
{
    const UUID = '41d42dcb-2429-4183-82d5-7c3a04a36a1b';
    const EVENT_NAME = TypeOfEvent::ELEMENT_CREATION;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeType::UUID
    ];

}

