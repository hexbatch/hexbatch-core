<?php


namespace App\Sys\Res\Types\Stock\System;

use App\Sys\Res\Attributes\Stock\System\MetaData\Metadata;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\SystemType;


class Meta extends BaseType
{
    const UUID = 'e11798f3-f23c-46b1-95a4-c868bb5e0f16';
    const TYPE_NAME = 'meta';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        Metadata::UUID,
    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}
