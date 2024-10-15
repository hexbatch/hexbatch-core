<?php
//  this is some news or message


namespace App\Sys\Res\Types\Stk\Root\Content;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Content;

class Blog extends BaseType
{
    const UUID = 'a7c4459f-ca1c-44e6-9418-12e99d8ea2f2';
    const TYPE_NAME = 'blog';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
    ];

    const PARENT_UUIDS = [
        Content::UUID
    ];

}

