<?php

namespace App\Sys\Res\Types\Stk\System\Placeholder;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Placeholder;


class CurrentSet extends BaseType
{
    const UUID = '14ef9d86-76be-446d-8ad6-ed5ac56fe5f1';
    const TYPE_NAME = 'current_set';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Placeholder::UUID
    ];

}

