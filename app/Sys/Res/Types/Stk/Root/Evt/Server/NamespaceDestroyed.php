<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class NamespaceDestroyed extends Evt\ScopeServer
{
    const UUID = 'af3524d0-8c56-4c74-99e3-337a6238c01c';
    const EVENT_NAME = TypeOfEvent::NAMESPACE_DESTROYED;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

