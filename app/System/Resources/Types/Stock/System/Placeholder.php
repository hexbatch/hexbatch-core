<?php

namespace App\System\Resources\Types\Stock\System;

use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\SystemType;


class Placeholder extends BaseType
{
    const UUID = '4d1910aa-c16f-4fca-b8c0-e84094d2d76a';
    const TYPE_NAME = 'placeholder';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}

