<?php

namespace App\Sys\Res\Types\Stk\Root\Signal\Semaphore;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Container;
use App\Sys\Res\Types\Stk\Root\Signal\Semaphore;


class SemaphoreWaitingSetType extends BaseType
{
    const UUID = '6223cf2b-d65f-414d-9247-6ad1941c5580';
    const TYPE_NAME = 'signal_semaphore_waiting';





    const PARENT_CLASSES = [
        Semaphore::class,
        Container::class
    ];

}

