<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class SetDestroying extends Evt\ScopeServer
{
    const UUID = '13171ed8-c166-45c6-9d10-634820343ec9';
    const EVENT_NAME = TypeOfEvent::SET_DESTROYING;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

