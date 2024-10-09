<?php
//  this is some news or message


namespace App\Sys\Res\Types\Stock\System\Content;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Content;

class News extends BaseType
{
    const UUID = 'f1d04677-c949-4014-b7d1-3f2a9cd03c1f';
    const TYPE_NAME = 'news';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
    ];

    const PARENT_UUIDS = [
        Content::UUID
    ];

}

