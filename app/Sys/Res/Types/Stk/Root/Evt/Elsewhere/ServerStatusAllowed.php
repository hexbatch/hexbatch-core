<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ServerStatusAllowed extends Evt\ScopeSet
{
    const UUID = 'e50f984a-cf9c-42b1-b180-390dcc23ef90';
    const EVENT_NAME = TypeOfEvent::SERVER_STATUS_ALLOWED;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

