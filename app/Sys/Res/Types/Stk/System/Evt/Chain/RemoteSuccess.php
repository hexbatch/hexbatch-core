<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Chain;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class RemoteSuccess extends Evt\ScopeChain
{
    const UUID = 'c048b73e-16af-452e-a05d-ab022e4a8065';
    const EVENT_NAME = TypeOfEvent::REMOTE_SUCCESS;
    const TYPE_NAME = TypeOfEvent::REMOTE_SUCCESS;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeChain::UUID
    ];

}

