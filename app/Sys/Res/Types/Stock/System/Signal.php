<?php


namespace App\Sys\Res\Types\Stock\System;

use App\Sys\Res\Attributes\Stock\System\MetaData\Metadata;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\SystemType;


class Signal extends BaseType
{
    const UUID = '712aae22-0e42-4a3d-917f-b0ec9bd8fa78';
    const TYPE_NAME = 'signal';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}
