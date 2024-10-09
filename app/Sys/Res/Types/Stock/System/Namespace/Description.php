<?php

namespace App\Sys\Res\Types\Stock\System\Namespace;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\NamespaceType;


class Description extends BaseType
{
    const UUID = 'd422d4f8-636e-45ff-9869-c64b089d36b8';
    const TYPE_NAME = 'description';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        NamespaceType::class
    ];

}

