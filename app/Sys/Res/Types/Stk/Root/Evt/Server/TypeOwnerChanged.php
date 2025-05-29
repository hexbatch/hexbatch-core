<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class TypeOwnerChanged extends Evt\ScopeServer
{
    const UUID = '451b9426-15cc-48f7-92f0-e361b5a0ab2a';
    const EVENT_NAME = TypeOfEvent::TYPE_OWNER_CHANGED;

    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

