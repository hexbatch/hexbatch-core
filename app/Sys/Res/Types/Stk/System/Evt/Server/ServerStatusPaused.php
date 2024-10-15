<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class ServerStatusPaused extends Evt\ScopeSet
{
    const UUID = 'f1eccb62-07f2-4290-8f76-f0898e35704f';
    const EVENT_NAME = TypeOfEvent::SERVER_STATUS_PAUSED;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeServer::UUID
    ];

}

