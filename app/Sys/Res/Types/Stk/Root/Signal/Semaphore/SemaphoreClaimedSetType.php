<?php

namespace App\Sys\Res\Types\Stk\Root\Signal\Semaphore;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Container;
use App\Sys\Res\Types\Stk\Root\Signal\Semaphore;


class SemaphoreClaimedSetType extends BaseType
{
    const UUID = 'dd6986ab-bf28-45e5-b2a8-63f73e2b1914';
    const TYPE_NAME = 'signal_semaphore_claimed';





    const PARENT_CLASSES = [
        Semaphore::class,
        Container::class
    ];

}

