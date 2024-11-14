<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class TypeDeleted extends Evt\ScopeServer
{
    const UUID = '85773d62-160e-4365-8e23-cc72e6d22d84';
    const EVENT_NAME = TypeOfEvent::TYPE_DELETED;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

