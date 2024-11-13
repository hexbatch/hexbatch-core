<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class AttributeHandleRemoved extends Evt\ScopeSet
{
    const UUID = '0f2b18d8-fa6c-4531-9753-8dfcd70d0405';
    const EVENT_NAME = TypeOfEvent::ATTRIBUTE_HANDLE_REMOVED;




    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

