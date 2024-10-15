<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class ShapeAdded extends Evt\ScopeSet
{
    const UUID = 'b2f7d7b0-c1b9-4e6b-abad-2176b7c2009a';
    const EVENT_NAME = TypeOfEvent::SHAPE_ADDED;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeSet::UUID
    ];

}

