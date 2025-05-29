<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class LinkDestroying extends Evt\ScopeServer
{
    const UUID = 'fcdeae99-6b45-4183-8c7a-a3511e18ec3b';
    const EVENT_NAME = TypeOfEvent::LINK_DESTROYING;


    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

