<?php

namespace App\System\Resources\Types\Stock\System;

use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\SystemType;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class GroupOperation extends BaseType
{
    const UUID = 'ae7a8d52-f1f9-4740-9db5-0df3e5819cd4';
    const TYPE_NAME = 'group_operation';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}

