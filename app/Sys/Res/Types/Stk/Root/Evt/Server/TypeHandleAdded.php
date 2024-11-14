<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class TypeHandleAdded extends Evt\ScopeServer
{
    const UUID = 'a886f027-a54f-40fb-8049-d76624f6b5ca';
    const EVENT_NAME = TypeOfEvent::TYPE_HANDLE_ADDED;


    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

