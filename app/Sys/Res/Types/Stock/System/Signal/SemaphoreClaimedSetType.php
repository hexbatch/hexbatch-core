<?php

namespace App\Sys\Res\Types\Stock\System\Signal;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Container;


class SemaphoreClaimedSetType extends BaseType
{
    const UUID = 'dd6986ab-bf28-45e5-b2a8-63f73e2b1914';
    const TYPE_NAME = 'signal_semaphore_claimed';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Semaphore::UUID,
        Container::UUID
    ];

}

