<?php

namespace App\Sys\Res\Types\Stk\Root\Media;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Media;


class Audio extends BaseType
{
    const UUID = '01dbfb91-6a6c-49fa-9208-cfad3b0ffc77';
    const TYPE_NAME = 'media_audio';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Media::UUID
    ];

}

