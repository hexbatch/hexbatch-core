<?php

namespace App\System\Resources\Types\Stock\System\Meta\MetaLanguage;

use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\System\Meta\MetaRegion;


class En extends BaseType
{
    const UUID = '89f28e67-46bd-4bd2-a3c3-2671d7efecaf';
    const TYPE_NAME = 'english';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        MetaRegion::UUID
    ];

}

