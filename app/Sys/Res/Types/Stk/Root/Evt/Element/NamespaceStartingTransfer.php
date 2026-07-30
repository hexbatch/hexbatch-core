<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Element\Traits\ElementBlockingEventTree;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;


class NamespaceStartingTransfer extends Evt\ScopeElement  implements ICommandCallable, Traits\IElementEvent
{
    use ElementBlockingEventTree;
    const UUID = 'a49e7a9c-15eb-4d1d-9f5e-b0d1bb37e9eb';
    const EVENT_NAME = TypeOfEvent::NAMESPACE_STARTING_TRANSFER;


    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];


    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($children_args,$command_args,'starting transfer');
    }

    protected function decide() : bool {
        return true;
    }

}

