<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class TypeRetired extends Evt\ScopeServer
{
    const UUID = '1c54055a-1a62-4df4-bf3c-4ebf462cf659';
    const EVENT_NAME = TypeOfEvent::TYPE_RETIRED;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

