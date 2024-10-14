<?php

namespace App\Sys\Res\Types\Stk\System\Placeholder;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Placeholder;


class CurrentElement extends BaseType
{
    const UUID = '8d00f363-c6bd-4415-8aae-a39d1576b67e';
    const TYPE_NAME = 'current_element';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Placeholder::UUID
    ];

}

