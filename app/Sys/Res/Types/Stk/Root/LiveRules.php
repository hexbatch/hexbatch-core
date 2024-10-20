<?php

namespace App\Sys\Res\Types\Stk\Root;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

/*
 * Type must have this in up-type for live rules to be used on it
 * Elements can only be given energy if this is included up-type
 */
class LiveRules extends BaseType
{
    const UUID = '92c139b6-c99a-4213-b992-e8075517c785';
    const TYPE_NAME = 'live_rules';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Root::UUID
    ];

}

