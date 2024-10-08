<?php


namespace App\System\Resources\Types\Stock\System;

use App\System\Resources\Attributes\Stock\System\MetaData\Metadata;
use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\SystemType;


class Meta extends BaseType
{
    const UUID = 'e11798f3-f23c-46b1-95a4-c868bb5e0f16';
    const TYPE_NAME = 'meta';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        Metadata::UUID,
    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}
