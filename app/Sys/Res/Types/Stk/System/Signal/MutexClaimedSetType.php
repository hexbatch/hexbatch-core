<?php

namespace App\Sys\Res\Types\Stk\System\Signal;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Container;


class MutexClaimedSetType extends BaseType
{
    const UUID = '40396ffb-8d66-4860-aae8-4822daad69dd';
    const TYPE_NAME = 'signal_mutex_claimed';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Mutex::UUID,
        Container::UUID
    ];

}
