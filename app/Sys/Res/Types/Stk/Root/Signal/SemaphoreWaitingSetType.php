<?php

namespace App\Sys\Res\Types\Stk\Root\Signal;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Container;


class SemaphoreWaitingSetType extends BaseType
{
    const UUID = '6223cf2b-d65f-414d-9247-6ad1941c5580';
    const TYPE_NAME = 'signal_semaphore_waiting';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Semaphore::class,
        Container::class
    ];

}

