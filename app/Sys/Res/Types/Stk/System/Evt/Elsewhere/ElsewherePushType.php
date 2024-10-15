<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class ElsewherePushType extends Evt\ScopeSet
{
    const UUID = '92245b59-df9a-4ad6-b9f6-fa21e1cfcb8f';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_PUSH_TYPE;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeElsewhere::UUID
    ];

}

