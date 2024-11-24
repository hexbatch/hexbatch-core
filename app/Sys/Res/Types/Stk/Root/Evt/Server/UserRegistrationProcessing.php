<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class UserRegistrationProcessing extends Evt\ScopeServer
{
    const UUID = 'fae98108-ccc8-465f-a459-fa87a17ae2a0';
    const EVENT_NAME = TypeOfEvent::USER_REGISTRATION_PROCESSING;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

