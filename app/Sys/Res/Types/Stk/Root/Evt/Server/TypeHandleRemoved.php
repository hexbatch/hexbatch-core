<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class TypeHandleRemoved extends Evt\ScopeServer
{
    const UUID = 'be2a09b2-9c5a-47e4-ac38-bc8343e3a510';
    const EVENT_NAME = TypeOfEvent::TYPE_HANDLE_REMOVED;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

