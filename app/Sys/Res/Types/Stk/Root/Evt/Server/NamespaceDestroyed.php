<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Server\Traits\ServerEventTree;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;


class NamespaceDestroyed extends Evt\ScopeServer implements ICommandCallable, Traits\IServerEvent
{
    use ServerEventTree;

    const UUID = 'af3524d0-8c56-4c74-99e3-337a6238c01c';
    const EVENT_NAME = TypeOfEvent::NAMESPACE_DESTROYED;


    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'Namespace destroyed~ ');
    }

}

