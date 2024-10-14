<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Set;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Evt;


class Read extends BaseType
{
    const UUID = '333a57fc-8472-4d88-b69e-a63ac64fe642';
    const TYPE_NAME = 'event_read';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeServer::UUID
    ];

}

