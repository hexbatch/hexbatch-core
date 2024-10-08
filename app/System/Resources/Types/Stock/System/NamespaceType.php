<?php


namespace App\System\Resources\Types\Stock\System;

use App\System\Resources\Attributes\Stock\System\MetaData\NamespaceType\NamespaceData;
use App\System\Resources\Attributes\Stock\System\MetaData\NamespaceType\NamespaceData\Address;
use App\System\Resources\Attributes\Stock\System\MetaData\NamespaceType\NamespaceData\ContactInfo;
use App\System\Resources\Attributes\Stock\System\MetaData\NamespaceType\NamespaceData\MapLocation;
use App\System\Resources\Attributes\Stock\System\MetaData\NamespaceType\NamespaceData\Name;
use App\System\Resources\Attributes\Stock\System\MetaData\NamespaceType\NamespaceData\Timezone;
use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\SystemType;


class NamespaceType extends BaseType
{
    const UUID = 'f6952b0a-cf14-46c6-9695-36f489fbc732';
    const TYPE_NAME = 'namespace';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        NamespaceData::UUID,
        Name::UUID,
        Address::UUID,
        ContactInfo::UUID,
        MapLocation::UUID,
        Timezone::UUID,
    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}



