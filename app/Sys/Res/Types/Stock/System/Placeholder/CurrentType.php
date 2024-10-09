<?php

namespace App\Sys\Res\Types\Stock\System\Placeholder;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Placeholder;


class CurrentType extends BaseType
{
    const UUID = '12f194f6-1328-4641-a498-0b482d4dd30d';
    const TYPE_NAME = 'current_type';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Placeholder::UUID
    ];

}

