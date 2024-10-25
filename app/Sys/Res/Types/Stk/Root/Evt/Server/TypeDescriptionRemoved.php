<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class TypeDescriptionRemoved extends Evt\ScopeSet
{
    const UUID = 'be2a09b2-9c5a-47e4-ac38-bc8343e3a510';
    const EVENT_NAME = TypeOfEvent::TYPE_DESCRIPTION_REMOVED;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

