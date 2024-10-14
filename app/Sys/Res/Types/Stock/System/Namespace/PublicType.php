<?php

namespace App\Sys\Res\Types\Stock\System\Namespace;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\NamespaceType;


class PublicType extends BaseType
{
    const UUID = 'dc6a24eb-a9fa-4946-9f0e-d152f65d8a97';
    const TYPE_NAME = 'public';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        NamespaceType::UUID
    ];

}

