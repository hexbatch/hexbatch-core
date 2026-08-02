<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Server\Traits\ServerBlockingEventTree;
use App\Sys\Res\Types\Stk\Root\Evt\Server\Traits\ServerEventTree;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;

/**
 * there was a phase edit action and the type was added into one or more places
 */
class LivePermissionAdding extends Evt\ScopeServer implements ICommandCallable
{
    use ServerEventTree;
    const UUID = '63a07681-0023-414f-bee8-44ad22c1613b';
    const EVENT_NAME = TypeOfEvent::LIVE_PERMISSION_ADDING;


    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];



    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'Live permission adding~ ');
    }

}

