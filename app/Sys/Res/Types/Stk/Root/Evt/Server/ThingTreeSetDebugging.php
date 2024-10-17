<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Evt;

/*
 * set thing_settings on root or children as needed
 */
class ThingTreeSetDebugging extends Evt\ScopeSet
{
    const UUID = '41c60434-8cd2-4982-b914-3b416c1c7012';
    const EVENT_NAME = TypeOfEvent::THING_TREE_SET_DEBUGGING;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeServer::UUID
    ];

}

