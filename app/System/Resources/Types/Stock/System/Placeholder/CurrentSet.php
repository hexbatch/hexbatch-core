<?php

namespace App\System\Resources\Types\Stock\System\Placeholder;

use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\System\Placeholder;


class CurrentSet extends BaseType
{
    const UUID = '14ef9d86-76be-446d-8ad6-ed5ac56fe5f1';
    const TYPE_NAME = 'current_set';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Placeholder::class
    ];

}

