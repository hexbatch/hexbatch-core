<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class NamespaceAdminAdding extends Evt\ScopeElement
{
    const UUID = '00e105a0-5b7f-4a8c-b80f-84f8f83b56ba';
    const EVENT_NAME = TypeOfEvent::NAMESPACE_ADMIN_ADDING;
    const TYPE_NAME =  self::EVENT_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeElement::UUID
    ];

}

