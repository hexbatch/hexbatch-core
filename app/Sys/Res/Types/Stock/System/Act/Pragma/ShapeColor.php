<?php

namespace App\Sys\Res\Types\Stock\System\Act\Pragma;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Act;


class ShapeColor extends BaseType
{
    const UUID = '6280f4c3-f2de-49c1-8b4e-5f3e7aab008c';
    const TYPE_NAME = 'pragma_shape_color';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Pragma::UUID
    ];

}

