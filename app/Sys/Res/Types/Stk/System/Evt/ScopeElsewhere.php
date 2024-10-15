<?php

namespace App\Sys\Res\Types\Stk\System\Evt;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;


class ScopeElsewhere extends BaseEvent
{
    const UUID = 'f57fa3b6-a1be-4af8-b4a6-2590cd854e4e';
    const TYPE_NAME = 'scope_elsewhere';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        BaseEvent::UUID
    ];

}

