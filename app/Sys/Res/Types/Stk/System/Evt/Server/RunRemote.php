<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class RunRemote extends Evt\ScopeServer
{
    const UUID = '09e8b85d-4b1d-4500-ab32-f8ae03dd1af9';
    const EVENT_NAME = TypeOfEvent::RUN_REMOTE;
    const TYPE_NAME = TypeOfEvent::RUN_REMOTE;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeServer::UUID
    ];

}

