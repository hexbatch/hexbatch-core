<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class Read extends Evt\ScopeSet
{
    const UUID = '333a57fc-8472-4d88-b69e-a63ac64fe642';
    const EVENT_NAME = TypeOfEvent::ATTRIBUTE_READ;
    const TYPE_NAME = TypeOfEvent::ATTRIBUTE_READ;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeSet::UUID
    ];

}

