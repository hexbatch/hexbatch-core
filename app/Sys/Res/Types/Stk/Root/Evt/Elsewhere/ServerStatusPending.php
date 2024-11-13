<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ServerStatusPending extends Evt\ScopeSet
{
    const UUID = '6fb67464-5f78-4db8-957c-354ea7a58440';
    const EVENT_NAME = TypeOfEvent::SERVER_STATUS_PENDING;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

