<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;


class LinkDestroyed extends Evt\ScopeServer implements ICommandCallable
{
    use Evt\Element\Traits\ElementNotificationEventTree;
    const UUID = 'd5cdc981-8bbd-495d-b58d-c917d908ae88';
    const EVENT_NAME = TypeOfEvent::LINK_DESTROYED;

    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

    /** @throws \Throwable */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'link removed~ ');
    }

}

