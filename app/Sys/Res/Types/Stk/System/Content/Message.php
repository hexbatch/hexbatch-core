<?php
//  this is some news or message


namespace App\Sys\Res\Types\Stk\System\Content;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Content;

class Message extends BaseType
{
    const UUID = 'e7d58805-4e49-4deb-a348-b3bd5df1860f';
    const TYPE_NAME = 'message';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
    ];

    const PARENT_UUIDS = [
        Content::UUID
    ];

}

