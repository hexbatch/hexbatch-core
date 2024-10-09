<?php

namespace App\Sys\Res\Types\Stock\System\Meta\Language;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Meta;


class En extends BaseType
{
    const UUID = '89f28e67-46bd-4bd2-a3c3-2671d7efecaf';
    const TYPE_NAME = 'english';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Meta\Region::UUID
    ];

}

