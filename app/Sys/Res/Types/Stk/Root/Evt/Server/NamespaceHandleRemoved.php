<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class NamespaceHandleRemoved extends Evt\ScopeServer
{
    const UUID = '7d84a655-a0e4-47b3-ae1a-6081133c5828';
    const EVENT_NAME = TypeOfEvent::NAMESPACE_HANDLE_REMOVED;


    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

