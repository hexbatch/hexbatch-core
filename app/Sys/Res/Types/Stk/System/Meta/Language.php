<?php

namespace App\Sys\Res\Types\Stk\System\Meta;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Meta;


class Language extends BaseType
{
    const UUID = '03df721a-0082-48c8-883a-377cf2992105';
    const TYPE_NAME = 'meta_language';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Meta::UUID
    ];

}

