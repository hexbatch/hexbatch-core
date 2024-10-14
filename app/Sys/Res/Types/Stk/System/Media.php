<?php


namespace App\Sys\Res\Types\Stk\System;

use App\Sys\Res\Atr\Stk\Media\DownloadableUrl;
use App\Sys\Res\Atr\Stk\Media\MediaUrl;
use App\Sys\Res\Atr\Stk\MetaData\Media\MediaData;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\SystemType;


class Media extends BaseType
{
    const UUID = 'e11798f3-f23c-46b1-95a4-c868bb5e0f16';
    const TYPE_NAME = 'media';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        MediaData::UUID,
        MediaUrl::UUID,
        DownloadableUrl::UUID,
        MediaData::UUID,
    ];

    const PARENT_UUIDS = [
        SystemType::UUID,
    ];

}
