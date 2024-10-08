<?php

namespace App\System\Resources\Types\Stock\System\Namespace;

use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\System\NamespaceType;


class PrivateType extends BaseType
{
    const UUID = '8cc1bf4f-90ef-4b85-8b87-ba00ab1b8049';
    const TYPE_NAME = 'private';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        NamespaceType::class
    ];

}

