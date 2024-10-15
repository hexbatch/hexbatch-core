<?php


namespace App\Sys\Res\Types\Stk\Root;

use App\Sys\Res\Atr\Stk\MetaData\NamespaceType\NamespaceData;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;


class NamespaceType extends BaseType
{
    const UUID = 'f6952b0a-cf14-46c6-9695-36f489fbc732';
    const TYPE_NAME = 'namespace';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        NamespaceData::UUID
    ];

    const PARENT_UUIDS = [
        Root::UUID
    ];

}



