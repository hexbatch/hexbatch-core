<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class SetDestroyed extends Evt\ScopeSet
{
    const UUID = '474374cd-555c-4b29-af01-29bd61f9bffd';
    const EVENT_NAME = TypeOfEvent::SET_DESTROYED;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

