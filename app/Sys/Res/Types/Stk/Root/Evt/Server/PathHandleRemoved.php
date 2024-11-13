<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class PathHandleRemoved extends Evt\ScopeSet
{
    const UUID = '353ebac1-2131-4555-90f5-6aa5b7b5372a';
    const EVENT_NAME = TypeOfEvent::PATH_HANDLE_REMOVED;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

