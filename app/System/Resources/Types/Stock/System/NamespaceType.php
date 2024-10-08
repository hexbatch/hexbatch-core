<?php
//todo all namespace elements and sets and descriptors inherit this

/*
 * attributes that do not inherit by subtype, must have these as parent attr
 * name (first last etc)
 * email
 * address stuff
 * phone
 * map location
 * timezone
 */


namespace App\System\Resources\Types\Stock\System;

use App\System\Resources\Attributes\Stock\System\NamespaceType\Address;
use App\System\Resources\Attributes\Stock\System\NamespaceType\Email;
use App\System\Resources\Attributes\Stock\System\NamespaceType\FirstName;
use App\System\Resources\Attributes\Stock\System\NamespaceType\LastName;
use App\System\Resources\Attributes\Stock\System\NamespaceType\MapLocation;
use App\System\Resources\Attributes\Stock\System\NamespaceType\Phone;
use App\System\Resources\Attributes\Stock\System\NamespaceType\Timezone;
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
        FirstName::UUID,
        LastName::UUID,
        Address::UUID,
        Email::UUID,
        MapLocation::UUID,
        Phone::UUID,
        Timezone::UUID,
    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}



