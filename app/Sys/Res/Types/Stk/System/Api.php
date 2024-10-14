<?php

namespace App\Sys\Res\Types\Stk\System;



use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\SystemType;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Api extends BaseType
{
    const UUID = 'd314149a-0f51-4b1e-b954-590a890e7c44';
    const TYPE_NAME = 'api';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}

