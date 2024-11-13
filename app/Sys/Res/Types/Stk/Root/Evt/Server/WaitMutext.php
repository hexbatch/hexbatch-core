<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class WaitMutext extends Evt\ScopeSet
{
    const UUID = '3d680ada-79bd-498c-aad1-1ba98beabddf';
    const EVENT_NAME = TypeOfEvent::WAIT_MUTEX;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

