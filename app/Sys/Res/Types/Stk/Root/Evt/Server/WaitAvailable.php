<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class WaitAvailable extends Evt\ScopeSet
{
    const UUID = '03da5ec5-c944-4cd4-81ca-e0726948aae2';
    const EVENT_NAME = TypeOfEvent::WAIT_AVAILABLE;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

