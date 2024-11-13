<?php

namespace App\Sys\Res\Types\Stk\Root\Signal\Master;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Container;
use App\Sys\Res\Types\Stk\Root\Signal\MasterSemaphore;


class OuterSetType extends BaseType
{
    const UUID = '5a2ddced-403a-46af-b24b-8c21c86045a1';
    const TYPE_NAME = 'master_semaphore_outer_set';





    const PARENT_CLASSES = [
        MasterSemaphore::class,
        Container::class,
    ];

}

