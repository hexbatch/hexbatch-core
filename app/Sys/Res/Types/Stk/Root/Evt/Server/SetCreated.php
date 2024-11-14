<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class SetCreated extends Evt\ScopeServer
{
    const UUID = '21dcf822-13a1-4abd-a400-3c6b1e74b82b';
    const EVENT_NAME = TypeOfEvent::SET_CREATED;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

