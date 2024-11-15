<?php

namespace App\Sys\Res\Types\Stk\Root\Namespace;

use App\Sys\Res\Atr\Stk\MetaData\NamespaceType\NamespaceData\Address;
use App\Sys\Res\Atr\Stk\MetaData\NamespaceType\NamespaceData\ContactInfo;
use App\Sys\Res\Atr\Stk\MetaData\NamespaceType\NamespaceData\MapLocation;
use App\Sys\Res\Atr\Stk\MetaData\NamespaceType\NamespaceData\Name;
use App\Sys\Res\Atr\Stk\MetaData\NamespaceType\NamespaceData\Timezone;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\NamespaceType;


class BasePerNamespace extends NamespaceType
{
    const UUID = '69a7d7cc-e4c7-45b7-879d-a7127cde9c33';
    const TYPE_NAME = 'user_namespace';



    const ATTRIBUTE_CLASSES = [
        Name::class,
        Address::class,
        ContactInfo::class,
        MapLocation::class,
        Timezone::class,
    ];

    const PARENT_CLASSES = [
        NamespaceType::class
    ];

}

