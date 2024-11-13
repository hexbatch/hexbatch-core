<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class LinkDestroyed extends Evt\ScopeSet
{
    const UUID = 'd5cdc981-8bbd-495d-b58d-c917d908ae88';
    const EVENT_NAME = TypeOfEvent::LINK_DESTROYED;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

