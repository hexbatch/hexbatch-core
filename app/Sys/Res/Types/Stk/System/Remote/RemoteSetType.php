<?php

namespace App\Sys\Res\Types\Stk\System\Remote;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Container;
use App\Sys\Res\Types\Stk\System\Remote;


class RemoteSetType extends BaseType
{
    const UUID = '4cc78a36-2e1c-419c-bba9-4f96f78d607a';
    const TYPE_NAME = 'remote_set';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Remote::UUID,
        Container::UUID
    ];

}
