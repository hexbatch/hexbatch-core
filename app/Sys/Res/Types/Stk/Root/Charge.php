<?php

namespace App\Sys\Res\Types\Stk\Root;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

/*
 * Type must have this in up-type to be used as a charge
 */
class Charge extends BaseType
{
    const UUID = '20df91e4-0ded-48a0-89d6-934bb8e6821c';
    const TYPE_NAME = 'charge';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Root::UUID
    ];

}

