<?php

namespace App\Sys\Res\Types\Stk\System\SystemNamespaceTypes\System\ThisServer;

use App\Sys\Res\Ele\Stk\SystemNSElements\SystemDescriptionElement;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Namespace\BasePerNamespace;


class ThisServerNS extends BaseType
{
    const UUID = 'd422d4f8-636e-45ff-9869-c64b089d36b8';
    const TYPE_NAME = 'system_namespace';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = SystemDescriptionElement::UUID;

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        BasePerNamespace::UUID
    ];

}

