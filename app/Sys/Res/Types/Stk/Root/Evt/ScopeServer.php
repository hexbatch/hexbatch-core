<?php

namespace App\Sys\Res\Types\Stk\Root\Evt;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;


class ScopeServer extends BaseEvent
{
    const UUID = '935a55bc-fbf9-4ffd-a6f5-63761e7c027e';
    const TYPE_NAME = 'scope_server
    ';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        BaseEvent::UUID
    ];

}

