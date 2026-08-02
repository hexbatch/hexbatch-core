<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Server\Traits\ServerEventTree;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;


class TypeHandleRemoved extends Evt\ScopeServer implements ICommandCallable
{
    use ServerEventTree;
    const UUID = 'be2a09b2-9c5a-47e4-ac38-bc8343e3a510';
    const EVENT_NAME = TypeOfEvent::TYPE_HANDLE_REMOVED;

    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];


    /** * @throws \Throwable */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'Type handle removed~ ');
    }

}

