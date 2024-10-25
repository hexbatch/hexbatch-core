<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class WaitSemaphore extends Evt\ScopeSet
{
    const UUID = '28ccc870-c2e1-4a0e-9588-f55365a85b17';
    const EVENT_NAME = TypeOfEvent::WAIT_SEMAPHORE;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

