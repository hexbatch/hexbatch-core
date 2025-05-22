<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class UserRegistrationStarting extends Evt\ScopeServer
{
    const UUID = '467a2091-e5b6-4058-9438-98bc07638e88';
    const EVENT_NAME = TypeOfEvent::USER_REGISTRATION_STARTING;


    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];


}

