<?php

namespace App\System\Resources\Types\Stock\System\Meta;

use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\System\Meta;
use App\System\Resources\Types\Stock\SystemType;


class MetaRegion extends BaseType
{
    const UUID = '2c9fa05c-5259-42b0-a0d2-0de629e91522';
    const TYPE_NAME = 'meta_region';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Meta::UUID
    ];

}

