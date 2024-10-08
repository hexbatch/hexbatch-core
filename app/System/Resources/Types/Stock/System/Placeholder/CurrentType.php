<?php

namespace App\System\Resources\Types\Stock\System\Placeholder;

use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\System\Placeholder;


class CurrentType extends BaseType
{
    const UUID = '12f194f6-1328-4641-a498-0b482d4dd30d';
    const TYPE_NAME = 'current_type';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Placeholder::UUID
    ];

}

