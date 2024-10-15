<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class ElsewherePushSet extends Evt\ScopeSet
{
    const UUID = '81ec04ef-1d1a-43a2-b78a-dbf825d0f7ba';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_PUSH_SET;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeElsewhere::UUID
    ];

}

