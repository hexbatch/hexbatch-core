<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/*
 * set thing_settings on root or children as needed
 */
class ThingTreeSetDebugging extends Evt\ScopeSet
{
    const UUID = '41c60434-8cd2-4982-b914-3b416c1c7012';
    const EVENT_NAME = TypeOfEvent::THING_TREE_SET_DEBUGGING;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

