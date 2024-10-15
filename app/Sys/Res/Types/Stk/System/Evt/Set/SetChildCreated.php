<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class SetChildCreated extends Evt\ScopeSet
{
    const UUID = '5db9e2bd-3175-45e8-87bc-67b05969d727';
    const EVENT_NAME = TypeOfEvent::SET_CHILD_CREATED;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeSet::UUID
    ];

}

