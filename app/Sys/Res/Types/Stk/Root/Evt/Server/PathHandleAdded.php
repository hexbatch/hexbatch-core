<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class PathHandleAdded extends Evt\ScopeServer
{
    const UUID = '793fd5da-e6b8-4196-9071-eebbd37713e1';
    const EVENT_NAME = TypeOfEvent::PATH_HANDLE_ADDED;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

