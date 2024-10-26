<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


/**
 * does much like the above, just different global set to find the semaphore semaphore_waiting.
 * also can wait for multiple semaphore types, or different elements from the same type,
 * all of which must be ready;
 * when done the semaphore elements go back to the idle or waiting, based on the auto attribute
 */
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

