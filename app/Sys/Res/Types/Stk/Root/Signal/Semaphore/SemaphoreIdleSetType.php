<?php

namespace App\Sys\Res\Types\Stk\Root\Signal\Semaphore;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Container;
use App\Sys\Res\Types\Stk\Root\Signal\Semaphore;


class SemaphoreIdleSetType extends BaseType
{
    const UUID = '3b7b068e-7e97-41a5-bc10-ff21a7aaf7d0';
    const TYPE_NAME = 'signal_semaphore_idle';





    const PARENT_CLASSES = [
        Semaphore::class,
        Container::class
    ];

}

