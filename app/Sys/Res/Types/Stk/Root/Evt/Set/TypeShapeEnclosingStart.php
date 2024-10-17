<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Evt;


class TypeShapeEnclosingStart extends Evt\ScopeSet
{
    const UUID = '05f17f89-5d87-420a-af36-7a4b86ed613d';
    const EVENT_NAME = TypeOfEvent::TYPE_SHAPE_ENCLOSING_START;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeSet::UUID
    ];

}

