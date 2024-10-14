<?php

namespace App\Sys\Res\Types\Stk\System\Evt;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Event;


class ScopeSet extends BaseType
{
    const UUID = 'f7a8e981-e779-4e27-8a12-64c3361ab93b';
    const TYPE_NAME = 'events_scoped_to_set';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Event::UUID
    ];

}

