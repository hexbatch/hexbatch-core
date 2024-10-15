<?php

namespace App\Sys\Res\Types\Stk\System\Evt;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;


class ScopeType extends BaseEvent
{
    const UUID = '6d9ffce0-4922-4152-84c7-a9a5865b10bc';
    const TYPE_NAME = 'scope_type';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        BaseEvent::UUID
    ];

}

