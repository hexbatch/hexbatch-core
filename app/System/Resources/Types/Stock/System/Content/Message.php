<?php
//  this is some news or message


namespace App\System\Resources\Types\Stock\System\Content;

use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\System\Content;

class Message extends BaseType
{
    const UUID = 'e7d58805-4e49-4deb-a348-b3bd5df1860f';
    const TYPE_NAME = 'message';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
    ];

    const PARENT_UUIDS = [
        Content::UUID
    ];

}

