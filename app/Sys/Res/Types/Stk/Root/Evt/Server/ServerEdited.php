<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ServerEdited extends Evt\ScopeServer
{
    const UUID = '68e65076-8c20-4750-9078-8eebc3a7afef';
    const EVENT_NAME = TypeOfEvent::SERVER_EDITED;


    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

