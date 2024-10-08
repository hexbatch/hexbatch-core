<?php

namespace App\System\Resources\Types\Stock\System\Namespace;

use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\System\NamespaceType;


class Description extends BaseType
{
    const UUID = 'd422d4f8-636e-45ff-9869-c64b089d36b8';
    const TYPE_NAME = 'description';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        NamespaceType::class
    ];

}

