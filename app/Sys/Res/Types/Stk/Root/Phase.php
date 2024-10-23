<?php

namespace App\Sys\Res\Types\Stk\Root;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

/**
 * When new type published, and new row is created in @see \App\Models\Phase
 * When the type is destroyed, that corresponding row in the phase is destroyed
 */
class Phase extends BaseType
{
    const UUID = '1bb5ff53-6874-4914-afd9-4dc8c9534c8f';
    const TYPE_NAME = 'phase';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Root::UUID
    ];

}

