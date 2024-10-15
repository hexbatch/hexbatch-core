<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class TypeRetired extends Evt\ScopeSet
{
    const UUID = '1c54055a-1a62-4df4-bf3c-4ebf462cf659';
    const EVENT_NAME = TypeOfEvent::TYPE_RETIRED;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeServer::UUID
    ];

}

