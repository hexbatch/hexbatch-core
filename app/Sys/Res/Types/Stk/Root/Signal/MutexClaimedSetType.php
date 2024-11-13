<?php

namespace App\Sys\Res\Types\Stk\Root\Signal;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Container;


class MutexClaimedSetType extends BaseType
{
    const UUID = '40396ffb-8d66-4860-aae8-4822daad69dd';
    const TYPE_NAME = 'signal_mutex_claimed';





    const PARENT_CLASSES = [
        Mutex::class,
        Container::class
    ];

}

