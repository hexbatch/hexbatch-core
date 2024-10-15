<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class ShapeRemoved extends Evt\ScopeSet
{
    const UUID = '2cefa211-f66b-4e47-9d52-6fceb1132d4e';
    const EVENT_NAME = TypeOfEvent::SHAPE_REMOVED;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeSet::UUID
    ];

}

