<?php

namespace App\Sys\Res\Types\Stock\System\Placeholder\Namespace;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Namespace\BasePerNamespace;
use App\Sys\Res\Types\Stock\System\Placeholder;


class Other extends BaseType
{
    const UUID = 'db4a2344-7f63-4fe6-bda4-8a3c519df638';
    const TYPE_NAME = 'other';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Placeholder\CurrentNamespace::UUID,
        BasePerNamespace::UUID
    ];

}

