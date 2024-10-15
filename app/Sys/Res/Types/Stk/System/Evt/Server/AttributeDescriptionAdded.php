<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class AttributeDescriptionAdded extends Evt\ScopeSet
{
    const UUID = '2b4c800b-3716-41a9-9d0c-0e84516f06e2';
    const EVENT_NAME = TypeOfEvent::ATTRIBUTE_DESCRIPTION_ADDED;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeServer::UUID
    ];

}

