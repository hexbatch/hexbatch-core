<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ServerStatusBlocked extends Evt\ScopeElsewhere
{
    const UUID = '36b3b8c1-4f9e-4d57-a9de-64738fe97bdf';
    const EVENT_NAME = TypeOfEvent::SERVER_STATUS_BLOCKED;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

