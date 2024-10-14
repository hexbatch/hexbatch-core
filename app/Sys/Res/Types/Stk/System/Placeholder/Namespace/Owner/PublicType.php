<?php

namespace App\Sys\Res\Types\Stk\System\Placeholder\Namespace\Owner;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Placeholder\Namespace\Owner;


class PublicType extends BaseType
{
    const UUID = '4b9cf1b3-1568-4d2a-9be0-044d725783a4';
    const TYPE_NAME = 'owner_public';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Owner::UUID,
        \App\Sys\Res\Types\Stk\System\Namespace\PublicType::UUID
    ];

}

