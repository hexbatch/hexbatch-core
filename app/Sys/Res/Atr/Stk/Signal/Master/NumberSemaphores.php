<?php

namespace App\Sys\Res\Atr\Stk\Signal\Master;



use App\Sys\Res\Atr\BaseAttribute;
use App\Sys\Res\Atr\Stk\Signal\SignalBase;

/**
 * How many semaphores to keep at one time
 */
class NumberSemaphores extends BaseAttribute
{
    const UUID = 'e89a8401-09e9-4e25-b937-99a4af4970c4';
    const ATTRIBUTE_NAME = 'number_semaphores';
    const PARENT_ATTRIBUTE_CLASS = SignalBase::class;

}


