<?php

namespace App\Sys\Res\Types\Stk\Root\Signal\Master;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Container;
use App\Sys\Res\Types\Stk\Root\Signal\MasterSemaphore;


class OuterSetType extends BaseType
{
    const UUID = '5a2ddced-403a-46af-b24b-8c21c86045a1';
    const TYPE_NAME = 'master_semaphore_outer_set';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        MasterSemaphore::UUID,
        Container::UUID,
    ];

}

