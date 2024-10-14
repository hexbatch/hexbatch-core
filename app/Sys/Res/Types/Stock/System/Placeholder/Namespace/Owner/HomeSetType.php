<?php

namespace App\Sys\Res\Types\Stock\System\Placeholder\Namespace\Owner;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Placeholder\Namespace\Owner;


class HomeSetType extends BaseType
{
    const UUID = '77d5ba3d-4b0b-4d33-91ba-d5f909a1ebbf';
    const TYPE_NAME = 'owner_home_set';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Owner::UUID,
        \App\Sys\Res\Types\Stock\System\Namespace\HomeSet::UUID
    ];

}

