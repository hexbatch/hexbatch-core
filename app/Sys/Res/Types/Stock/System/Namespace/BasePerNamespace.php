<?php

namespace App\Sys\Res\Types\Stock\System\Namespace;

use App\Sys\Res\Attributes\Stock\System\MetaData\NamespaceType\NamespaceData\Address;
use App\Sys\Res\Attributes\Stock\System\MetaData\NamespaceType\NamespaceData\ContactInfo;
use App\Sys\Res\Attributes\Stock\System\MetaData\NamespaceType\NamespaceData\MapLocation;
use App\Sys\Res\Attributes\Stock\System\MetaData\NamespaceType\NamespaceData\Name;
use App\Sys\Res\Attributes\Stock\System\MetaData\NamespaceType\NamespaceData\Timezone;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\NamespaceType;


class BasePerNamespace extends BaseType
{
    const UUID = '69a7d7cc-e4c7-45b7-879d-a7127cde9c33';
    const TYPE_NAME = 'user_namespace';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        Name::UUID,
        Address::UUID,
        ContactInfo::UUID,
        MapLocation::UUID,
        Timezone::UUID,
    ];

    const PARENT_UUIDS = [
        NamespaceType::UUID
    ];

}

