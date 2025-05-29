<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class NamespaceHandleAdded extends Evt\ScopeServer
{
    const UUID = '44004444-4de3-4200-b2f3-27cac125be75';
    const EVENT_NAME = TypeOfEvent::NAMESPACE_HANDLE_ADDED;


    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

