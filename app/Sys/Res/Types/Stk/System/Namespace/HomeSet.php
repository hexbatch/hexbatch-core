<?php

namespace App\Sys\Res\Types\Stk\System\Namespace;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Container;
use App\Sys\Res\Types\Stk\System\NamespaceType;


class HomeSet extends BaseType
{
    const UUID = '3bf5302c-7ded-468a-af01-a19dc135c806';
    const TYPE_NAME = 'home_set';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        NamespaceType::UUID,
        Container::UUID
    ];

}

