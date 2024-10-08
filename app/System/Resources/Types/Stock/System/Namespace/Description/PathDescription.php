<?php

namespace App\System\Resources\Types\Stock\System\Namespace\Description;

use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\System\Namespace\Description;


class PathDescription extends BaseType
{
    const UUID = '3c9274cc-feb0-4a9d-9bd5-15ca28818478';
    const TYPE_NAME = 'path_description';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Description::class
    ];

}

