<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewhereSuspendedType extends Evt\ScopeSet
{
    const UUID = 'dbb69c4c-444c-4c43-929e-a16ad2eabceb';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_SUSPENDED_TYPE;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeElsewhere::UUID
    ];

}

