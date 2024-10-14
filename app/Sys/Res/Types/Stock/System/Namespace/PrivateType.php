<?php

namespace App\Sys\Res\Types\Stock\System\Namespace;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\NamespaceType;


class PrivateType extends BaseType
{
    const UUID = '8cc1bf4f-90ef-4b85-8b87-ba00ab1b8049';
    const TYPE_NAME = 'private';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        NamespaceType::UUID
    ];

}

