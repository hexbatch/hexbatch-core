<?php

namespace App\Sys\Res\Atr\Stk\Signal\Mutex;



use App\Sys\Res\Atr\BaseAttribute;
use App\Sys\Res\Atr\Stk\Signal\SignalBase;

class OncePerLifetime extends BaseAttribute
{
    const UUID = 'a02706e7-e41c-4687-8f05-e9540e8cf5f9';
    const ATTRIBUTE_NAME = 'once_per_lifetime';
    const PARENT_ATTRIBUTE_CLASS = SignalBase::class;

}


