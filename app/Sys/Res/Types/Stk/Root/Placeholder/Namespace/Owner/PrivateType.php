<?php

namespace App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\Owner;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\Owner;


class PrivateType extends BaseType
{
    const UUID = '12230abf-10ef-4b91-afee-f65f67430278';
    const TYPE_NAME = 'owner_private';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Owner::UUID,
        \App\Sys\Res\Types\Stk\Root\Namespace\PrivateType::UUID
    ];

}

