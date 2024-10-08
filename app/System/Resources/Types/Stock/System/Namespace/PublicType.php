<?php

namespace App\System\Resources\Types\Stock\System\Namespace;

use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\System\NamespaceType;


class PublicType extends BaseType
{
    const UUID = 'dc6a24eb-a9fa-4946-9f0e-d152f65d8a97';
    const TYPE_NAME = 'public';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        NamespaceType::class
    ];

}

