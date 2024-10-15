<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class LinkDescriptionRemoved extends Evt\ScopeSet
{
    const UUID = '1d378fdc-305f-41c6-b2f2-0b92392d7fc8';
    const EVENT_NAME = TypeOfEvent::LINK_DESCRIPTION_REMOVED;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeServer::UUID
    ];

}

