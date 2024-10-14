<?php

namespace App\Sys\Res\Types\Stk\System\Evt;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;


class ScopeChain extends BaseEvent
{
    const UUID = '0a725dbf-6937-4805-a002-3e8ee2163cee';
    const TYPE_NAME = 'events_scoped_to_chain';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        BaseEvent::UUID
    ];

}

