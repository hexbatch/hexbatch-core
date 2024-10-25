<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class TypeOwnerChange extends Evt\ScopeSet
{
    const UUID = '6c6fb95e-b5cb-43d0-a6bd-1e2ad69593d8';
    const EVENT_NAME = TypeOfEvent::TYPE_OWNER_CHANGE;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

