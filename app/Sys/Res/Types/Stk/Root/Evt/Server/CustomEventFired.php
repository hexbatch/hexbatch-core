<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Server\Traits\ServerEventTree;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;


class CustomEventFired extends Evt\ScopeServer implements ICommandCallable
{
    use ServerEventTree;
    const UUID = '8d7dc80e-2e38-4652-a47e-ce88124a456a';
    const EVENT_NAME = TypeOfEvent::CUSTOM_EVENT_FIRED;




    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

    /** * @throws \Throwable */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'Custom event fired~ ');
    }

}

