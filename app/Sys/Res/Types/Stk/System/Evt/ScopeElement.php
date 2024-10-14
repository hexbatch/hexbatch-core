<?php

namespace App\Sys\Res\Types\Stk\System\Evt;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Event;


class ScopeElement extends BaseType
{
    const UUID = 'cc593a6d-76ce-4835-9f43-2a1f14699647';
    const TYPE_NAME = 'events_scoped_to_element';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Event::UUID
    ];

}

