<?php

namespace App\System\Resources\Types\Stock\System\Meta;

use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\System\Meta;


class MetaLanguage extends BaseType
{
    const UUID = '03df721a-0082-48c8-883a-377cf2992105';
    const TYPE_NAME = 'meta_language';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Meta::UUID
    ];

}

