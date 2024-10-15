<?php

namespace App\Sys\Res\Types\Stk\Root\Signal;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Container;


class SemaphoreWaitingSetType extends BaseType
{
    const UUID = '6223cf2b-d65f-414d-9247-6ad1941c5580';
    const TYPE_NAME = 'signal_semaphore_waiting';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Semaphore::UUID,
        Container::UUID
    ];

}

