<?php

namespace App\Sys\Res\Types\Stk\Root\Api;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Api;


class TypeApi extends BaseType
{
    const UUID = '0c44a7dc-be18-4de4-a2b0-0a330f3efd43';
    const TYPE_NAME = 'type_api';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Api::UUID
    ];

}

