<?php

namespace App\Sys\Res\Types\Stk\System\Media;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Media;


class Image extends BaseType
{
    const UUID = '13e584b6-ac9e-4f18-97e8-55523a8a151d';
    const TYPE_NAME = 'media_image';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Media::UUID
    ];

}

