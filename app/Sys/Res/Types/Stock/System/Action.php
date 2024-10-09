<?php

namespace App\Sys\Res\Types\Stock\System;



use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\SystemType;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Action extends BaseType
{
    const UUID = '5453ef40-affb-4ea0-91dd-d3f998542288';
    const TYPE_NAME = 'action';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}

