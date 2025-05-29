<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class AttributeHandleAdded extends Evt\ScopeServer
{
    const UUID = '2b4c800b-3716-41a9-9d0c-0e84516f06e2';
    const EVENT_NAME = TypeOfEvent::ATTRIBUTE_HANDLE_ADDED;





    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

