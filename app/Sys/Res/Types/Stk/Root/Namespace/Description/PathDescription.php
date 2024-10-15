<?php

namespace App\Sys\Res\Types\Stk\Root\Namespace\Description;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Namespace\Description;


class PathDescription extends BaseType
{
    const UUID = '3c9274cc-feb0-4a9d-9bd5-15ca28818478';
    const TYPE_NAME = 'path_description';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Description::UUID
    ];

}

