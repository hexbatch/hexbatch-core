<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * This goes to the type  when a single element of a type is changed ownership or given for the first time
 */
class ElementOwnerChange extends Evt\ScopeSet
{
    const UUID = 'c43da607-84d1-40f5-992d-db4d091e6ec9';
    const EVENT_NAME = TypeOfEvent::ELEMENT_OWNER_CHANGE;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeType::UUID
    ];

}

