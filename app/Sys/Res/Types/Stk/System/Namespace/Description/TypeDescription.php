<?php

namespace App\Sys\Res\Types\Stk\System\Namespace\Description;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Namespace\Description;


class TypeDescription extends BaseType
{
    const UUID = '20b4215b-fa52-4bc2-896e-3b6475eb5ea6';
    const TYPE_NAME = 'path_description';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Description::UUID
    ];

}

