<?php

namespace App\Sys\Res\Types\Stk\Root\Signal\Master;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Container;
use App\Sys\Res\Types\Stk\Root\Signal\MasterSemaphore;


class ActionSetType extends BaseType
{
    const UUID = '351c20a0-233f-40e9-b2c3-d15c46b432a7';
    const TYPE_NAME = 'remote_rules_set';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        OuterSetType::UUID,
    ];

}

