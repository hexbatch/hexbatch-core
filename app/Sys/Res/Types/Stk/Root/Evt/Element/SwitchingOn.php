<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;

use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Element\Traits\ElementBlockingEventTree;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;



class SwitchingOn extends Evt\ScopeElement  implements ICommandCallable
{
    use ElementBlockingEventTree;

    const UUID = '96b76741-e3ff-46ad-bf59-d05cc03366e8';
    const EVENT_NAME = TypeOfEvent::SWITCHING_ON;

    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];






    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($children_args,$command_args,'turning on');
    }

    protected function decide() : bool {
        return true;
    }


}

