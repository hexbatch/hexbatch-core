<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Server\Traits\ServerNotificationEventTree;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;


class UserRegistered extends Evt\ScopeServer implements ICommandCallable
{
    use ServerNotificationEventTree;
    const UUID = 'fae98108-ccc8-465f-a459-fa87a17ae2a0';
    const EVENT_NAME = TypeOfEvent::USER_REGISTERED;

    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];


    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'Phase added~ ');
    }


}

