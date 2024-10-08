<?php
//  this is some news or message


namespace App\System\Resources\Types\Stock\System\Content;

use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\System\Content;

class Blog extends BaseType
{
    const UUID = 'a7c4459f-ca1c-44e6-9418-12e99d8ea2f2';
    const TYPE_NAME = 'blog';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
    ];

    const PARENT_UUIDS = [
        Content::UUID
    ];

}

