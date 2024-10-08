<?php

namespace App\System\Resources\Types\Stock\System\Namespace;

use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\System\NamespaceType;


class HomeSet extends BaseType
{
    const UUID = '3bf5302c-7ded-468a-af01-a19dc135c806';
    const TYPE_NAME = 'home_set';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        NamespaceType::class
    ];

}

