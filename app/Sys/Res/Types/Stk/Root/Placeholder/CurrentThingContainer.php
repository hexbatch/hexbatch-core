<?php

namespace App\Sys\Res\Types\Stk\Root\Placeholder;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Placeholder;


class CurrentThingContainer extends BaseType
{
    const UUID = '0b8423a1-1524-4188-89cf-38545f2867f4';
    const TYPE_NAME = 'current_thing';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Placeholder::UUID
    ];

}

