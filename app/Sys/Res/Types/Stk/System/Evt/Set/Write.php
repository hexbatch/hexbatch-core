<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class Write extends Evt\ScopeSet
{
    const UUID = 'a1b06d04-7ac4-43a1-8353-a3f9c7df1b94';
    const EVENT_NAME = TypeOfEvent::ATTRIBUTE_WRITE;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeSet::UUID
    ];

}

