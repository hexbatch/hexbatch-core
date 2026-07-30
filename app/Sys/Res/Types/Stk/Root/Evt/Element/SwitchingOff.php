<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;

use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Element\Traits\ElementBlockingEventTree;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;


class SwitchingOff extends Evt\ScopeElement  implements ICommandCallable
{
    use ElementBlockingEventTree;

    const UUID = 'ca462f72-13f6-4acc-8670-6380cef18244';
    const EVENT_NAME = TypeOfEvent::SWITCHING_OFF;


    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];


    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($children_args,$command_args,'turning off');
    }

    protected function decide() : bool {
        return true;
    }



}

