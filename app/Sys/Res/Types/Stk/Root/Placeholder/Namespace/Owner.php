<?php

namespace App\Sys\Res\Types\Stk\Root\Placeholder\Namespace;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Namespace\BasePerNamespace;
use App\Sys\Res\Types\Stk\Root\Placeholder;


class Owner extends BaseType
{
    const UUID = '05f77780-3de0-4e18-8ca6-12c0c4eb7508';
    const TYPE_NAME = 'current_owner';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Placeholder\CurrentNamespace::UUID,
        BasePerNamespace::UUID
    ];

}

