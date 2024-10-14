<?php

namespace App\Sys\Res\Types\Stock\System\Placeholder\Namespace\Other;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Placeholder\Namespace\Other;


class PrivateType extends BaseType
{
    const UUID = 'a5529dd2-833a-4310-bcff-adef52bedb09';
    const TYPE_NAME = 'ther_private';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Other::UUID,
        \App\Sys\Res\Types\Stock\System\Namespace\PrivateType::UUID
    ];

}

