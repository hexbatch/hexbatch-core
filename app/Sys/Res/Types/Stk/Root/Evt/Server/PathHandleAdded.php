<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Server\Traits\ServerEventTree;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;


class PathHandleAdded extends Evt\ScopeServer implements ICommandCallable
{
    use ServerEventTree;
    const UUID = '793fd5da-e6b8-4196-9071-eebbd37713e1';
    const EVENT_NAME = TypeOfEvent::PATH_HANDLE_ADDED;


    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

    /** * @throws \Throwable */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'Path handle added~ ');
    }

}

