<?php
//  this is some news or message


namespace App\System\Resources\Types\Stock\System\Content;

use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\System\Content;

class News extends BaseType
{
    const UUID = 'f1d04677-c949-4014-b7d1-3f2a9cd03c1f';
    const TYPE_NAME = 'news';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
    ];

    const PARENT_UUIDS = [
        Content::UUID
    ];

}

