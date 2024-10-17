<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Evt;

/*
 * set thing_settings on root or children as needed
 */
class ThingTreeSetPermissions extends Evt\ScopeSet
{
    const UUID = 'eff80c28-42e7-4e1c-804f-9ab154264550';
    const EVENT_NAME = TypeOfEvent::THING_TREE_SET_PERMISSIONS;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeServer::UUID
    ];

}

