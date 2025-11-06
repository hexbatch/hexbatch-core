<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;

/**
 * sent as after the fact, because permission already given in the events in the
 * @uses NamespaceStartingTransfer
 */
class NamespaceTransfered extends Evt\ScopeServer implements ICommandCallable, Traits\IServerEvent
{
    use \App\Sys\Res\Types\Stk\Root\Evt\Server\Traits\ServerNotificationEventTree;
    const UUID = '5fbaad48-f51b-466b-94f0-19a63264d808';
    const EVENT_NAME = TypeOfEvent::NAMESPACE_TRANSFERRED;



    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'Namespace transferred~ ');
    }

}

