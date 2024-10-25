<?php

namespace App\Sys\Res\Types\Stk\Root\Signal;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Container;


class MutexWaitingSetType extends BaseType
{
    const UUID = '1896ae29-7d76-42bf-80c5-111d18a93032';
    const TYPE_NAME = 'signal_mutex_waiting';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Mutex::class,
        Container::class
    ];

}

