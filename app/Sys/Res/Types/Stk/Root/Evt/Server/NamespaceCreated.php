<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Server\Traits\ServerNotificationEventTree;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;

class NamespaceCreated extends Evt\ScopeServer implements ICommandCallable
{
    use ServerNotificationEventTree;

    const UUID = '6ad6b92d-0cd0-4dd2-bc51-b2166e405a81';
    const EVENT_NAME = TypeOfEvent::NAMESPACE_CREATED;


    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];



    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'Namespace created~ ');
    }

}

