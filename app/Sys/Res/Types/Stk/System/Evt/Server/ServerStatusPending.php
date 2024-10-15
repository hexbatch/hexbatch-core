<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class ServerStatusPending extends Evt\ScopeSet
{
    const UUID = '6fb67464-5f78-4db8-957c-354ea7a58440';
    const EVENT_NAME = TypeOfEvent::SERVER_STATUS_PENDING;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeServer::UUID
    ];

}

