<?php

namespace App\Sys\Res\Types\Stk\Root\Media;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Media;


class Video extends BaseType
{
    const UUID = 'afa00e96-26ce-4c8c-9cc6-019ea1fead4c';
    const TYPE_NAME = 'media_video';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Media::UUID
    ];

}

