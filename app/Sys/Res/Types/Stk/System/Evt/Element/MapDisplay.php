<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class MapDisplay extends Evt\ScopeElement
{
    const UUID = '4a070471-0924-4a27-8ea0-3dbaf2e32a8e';
    const EVENT_NAME = TypeOfEvent::MAP_DISPLAY;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeElement::UUID
    ];

}

