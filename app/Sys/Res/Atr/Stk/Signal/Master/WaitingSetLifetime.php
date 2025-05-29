<?php

namespace App\Sys\Res\Atr\Stk\Signal\Master;



use App\Sys\Res\Atr\BaseAttribute;
use App\Sys\Res\Atr\Stk\Signal\SignalBase;

/**
 * if the action sets should be destroyed after the things waiting on it have completed
 */
class WaitingSetLifetime extends BaseAttribute
{
    const UUID = 'e33dd2ab-141f-46dc-9c0d-ff232490e2dd';
    const ATTRIBUTE_NAME = 'waiting_set_lifetime';
    const PARENT_ATTRIBUTE_CLASS = SignalBase::class;

}


