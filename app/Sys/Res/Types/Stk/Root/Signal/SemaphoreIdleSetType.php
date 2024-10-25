<?php

namespace App\Sys\Res\Types\Stk\Root\Signal;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Container;


class SemaphoreIdleSetType extends BaseType
{
    const UUID = '3b7b068e-7e97-41a5-bc10-ff21a7aaf7d0';
    const TYPE_NAME = 'signal_semaphore_idle';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Semaphore::class,
        Container::class
    ];

}

