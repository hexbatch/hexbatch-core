<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class Nothing extends Evt\ScopeServer
{
    const UUID = '2c3ad15e-ff2d-4f0e-b13d-21afcf272543';
    const EVENT_NAME = TypeOfEvent::NOTHING;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

