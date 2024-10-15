<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Api;


class Publish extends BaseType
{
    const UUID = '81c04881-39a5-4903-aaf2-34633b6f4f69';
    const TYPE_NAME = 'api_type_publish';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Api\TypeApi::UUID
    ];

}

