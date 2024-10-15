<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class ShapeDisplay extends Evt\ScopeElement
{
    const UUID = '336262b6-7c2d-46c4-82c7-3a9ef1720e31';
    const EVENT_NAME = TypeOfEvent::SHAPE_DISPLAY;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeElement::UUID
    ];

}

