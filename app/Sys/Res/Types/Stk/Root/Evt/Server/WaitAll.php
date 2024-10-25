<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class WaitAll extends Evt\ScopeSet
{
    const UUID = '5aa5f813-ed19-4c95-898d-54aa1d396cbc';
    const EVENT_NAME = TypeOfEvent::WAIT_ALL;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

