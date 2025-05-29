<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class TypeSuspended extends Evt\ScopeServer
{
    const UUID = '854315cf-3c96-4d54-a3c9-5daeafe3eeb3';
    const EVENT_NAME = TypeOfEvent::TYPE_SUSPENDED;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

