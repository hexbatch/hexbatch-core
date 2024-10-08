<?php

namespace App\System\Resources\Types\Stock\System;



use App\System\Resources\Attributes\Stock\System\Server\CommitHash;
use App\System\Resources\Attributes\Stock\System\Server\Version;
use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\SystemType;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Action extends BaseType
{
    const UUID = '5453ef40-affb-4ea0-91dd-d3f998542288';
    const TYPE_NAME = 'action';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}

