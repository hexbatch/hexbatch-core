<?php

namespace App\Sys\Res\Types\Stock\System\Placeholder;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\NamespaceType;
use App\Sys\Res\Types\Stock\System\Placeholder;


class CurrentNamespace extends BaseType
{
    const UUID = '86998a06-d158-402c-8250-8bc5257710f6';
    const TYPE_NAME = 'current_namespace';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Placeholder::UUID,
        NamespaceType::UUID,
    ];

}

