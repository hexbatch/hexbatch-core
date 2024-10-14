<?php

namespace App\Sys\Res\Types\Stk\System\Evt;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Event;


class ScopeServer extends BaseType
{
    const UUID = '935a55bc-fbf9-4ffd-a6f5-63761e7c027e';
    const TYPE_NAME = 'events_scoped_to_server';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Event::UUID
    ];

}

