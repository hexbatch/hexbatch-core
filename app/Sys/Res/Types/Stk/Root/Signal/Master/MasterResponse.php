<?php

namespace App\Sys\Res\Types\Stk\Root\Signal\Master;


use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Signal\MasterSemaphore;


class MasterResponse extends BaseType
{
    const UUID = '877cd316-2044-4018-895a-b43c528f080f';
    const TYPE_NAME = 'master_semaphore_response';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        MasterSemaphore::UUID
    ];

}

