<?php

namespace App\Sys\Res\Types\Stk\System\Meta;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Meta;


class Region extends BaseType
{
    const UUID = '2c9fa05c-5259-42b0-a0d2-0de629e91522';
    const TYPE_NAME = 'meta_region';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Meta::UUID
    ];

}

