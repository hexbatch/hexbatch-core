<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class ElementDestruction extends Evt\ScopeSet
{
    const UUID = '2c1cb906-04a6-4f7c-aceb-abd9f9598ad7';
    const EVENT_NAME = TypeOfEvent::ELEMENT_DESTRUCTION;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeType::UUID
    ];

}

