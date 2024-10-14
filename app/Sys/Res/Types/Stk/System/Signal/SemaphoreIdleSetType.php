<?php

namespace App\Sys\Res\Types\Stk\System\Signal;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Container;


class SemaphoreIdleSetType extends BaseType
{
    const UUID = '3b7b068e-7e97-41a5-bc10-ff21a7aaf7d0';
    const TYPE_NAME = 'signal_semaphore_idle';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Semaphore::UUID,
        Container::UUID
    ];

}

