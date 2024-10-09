<?php

namespace App\Sys\Res\Types\Stock\System;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\SystemType;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Container extends BaseType
{
    const UUID = '51e2fe0a-0087-4315-8324-fc9070a7d41d';
    const TYPE_NAME = 'container';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}

