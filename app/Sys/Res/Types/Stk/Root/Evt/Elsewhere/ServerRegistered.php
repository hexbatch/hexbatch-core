<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * Can cancel registration
 */

class ServerRegistered extends Evt\ScopeSet
{
    const UUID = 'c7b37d82-4e76-4900-88c7-c0098ae576d4';
    const EVENT_NAME = TypeOfEvent::SERVER_REGISTERED;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

