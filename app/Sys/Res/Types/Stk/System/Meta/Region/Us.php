<?php

namespace App\Sys\Res\Types\Stk\System\Meta\Region;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Meta;


class Us extends BaseType
{
    const UUID = 'ddbf00e9-faef-4e2f-a346-eaa46bae2489';
    const TYPE_NAME = 'region_usa';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Meta\Language::UUID
    ];

}

