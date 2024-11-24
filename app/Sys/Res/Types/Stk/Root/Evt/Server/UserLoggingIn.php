<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class UserLoggingIn extends Evt\ScopeServer
{
    const UUID = 'ce7204ce-1dc1-4f4a-b176-c8f3fff5aef9';
    const EVENT_NAME = TypeOfEvent::USER_LOGGING_IN;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

