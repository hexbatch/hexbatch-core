<?php

namespace App\Sys\Res\Types\Stock\System\Event\Chain;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Event;


class RemoteSuccess extends BaseType
{
    const UUID = 'c048b73e-16af-452e-a05d-ab022e4a8065';
    const TYPE_NAME = 'event_remote_success';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Event\ScopeChain::UUID
    ];

}

