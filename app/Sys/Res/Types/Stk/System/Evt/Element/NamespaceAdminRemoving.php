<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class NamespaceAdminRemoving extends Evt\ScopeElement
{
    const UUID = 'e342570b-7241-4af7-9d38-196fb2ff1363';
    const EVENT_NAME = TypeOfEvent::NAMESPACE_ADMIN_REMOVING;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeElement::UUID
    ];

}

