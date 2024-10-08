<?php


namespace App\System\Resources\Types\Stock\System;

use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\SystemType;


class Meta extends BaseType
{
    const UUID = 'e11798f3-f23c-46b1-95a4-c868bb5e0f16';
    const TYPE_NAME = 'meta';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}


/*
//todo descriptions that are not about appearance
* starts_at  iso_8601
* ends_at    iso_8601
* created_at iso_8601
* updated_at iso_8601
* joined_at  iso_8601

* author
* copywrite
* url
* rating
 * privacy
 * terms
* mime_type
* keywords
* language_code
* iso_region
 */
