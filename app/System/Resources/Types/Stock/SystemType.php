<?php

namespace App\System\Resources\Types\Stock;



use App\System\Resources\Attributes\Stock\Expiration;
use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class SystemType extends BaseType
{
    const UUID = '79a56b04-c36e-430f-bad4-5f53fb29ad4e';
    const TYPE_NAME = 'root';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        Expiration::UUID
    ];

    const PARENT_UUIDS = [];

}
