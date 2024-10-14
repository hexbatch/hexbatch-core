<?php

namespace App\Sys\Res\Types\Stock\System\Placeholder\Namespace\Other;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Placeholder\Namespace\Other;


class PublicType extends BaseType
{
    const UUID = '93d19ac0-dfd9-4c56-b2d9-96887505c7e2';
    const TYPE_NAME = 'other_public';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Other::UUID,
        \App\Sys\Res\Types\Stock\System\Namespace\PublicType::UUID
    ];

}

