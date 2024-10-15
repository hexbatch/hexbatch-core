<?php

namespace App\Sys\Res\Types\Stk\Root\Evt;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;


class ScopeElement extends BaseEvent
{
    const UUID = 'cc593a6d-76ce-4835-9f43-2a1f14699647';
    const TYPE_NAME = 'scope_element';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        BaseEvent::UUID
    ];

}

