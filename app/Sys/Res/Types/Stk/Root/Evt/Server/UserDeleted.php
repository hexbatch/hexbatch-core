<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Server\Traits\ServerNotificationEventTree;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;


class UserDeleted extends Evt\ScopeServer implements ICommandCallable, Traits\IServerEvent
{
    use ServerNotificationEventTree;
    const UUID = '3cb134f3-3143-41b3-b929-08e1c240349d';
    const EVENT_NAME = TypeOfEvent::USER_DELETED;



    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'User deleted~ ');
    }

}

