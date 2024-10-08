<?php

namespace App\System\Resources\Types\Stock\System\Namespace\Description;

use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\System\Namespace\Description;


class TypeDescription extends BaseType
{
    const UUID = '20b4215b-fa52-4bc2-896e-3b6475eb5ea6';
    const TYPE_NAME = 'path_description';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Description::class
    ];

}

