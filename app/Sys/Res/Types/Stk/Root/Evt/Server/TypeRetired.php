<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Server\Traits\ServerEventTree;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;


class TypeRetired extends Evt\ScopeServer implements ICommandCallable
{
    use ServerEventTree;
    const UUID = '1c54055a-1a62-4df4-bf3c-4ebf462cf659';
    const EVENT_NAME = TypeOfEvent::TYPE_RETIRED;


    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

    /** * @throws \Throwable */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'Type retired~ ');
    }

}

