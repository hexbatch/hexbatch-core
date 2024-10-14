<?php

namespace App\Sys\Res\Types\Stk\System;

use App\Sys\Res\Atr\Stk\Placeholder\PlaceholderAttribute;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\SystemType;


class Placeholder extends BaseType
{
    const UUID = '4d1910aa-c16f-4fca-b8c0-e84094d2d76a';
    const TYPE_NAME = 'placeholder';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        PlaceholderAttribute::UUID
    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}

