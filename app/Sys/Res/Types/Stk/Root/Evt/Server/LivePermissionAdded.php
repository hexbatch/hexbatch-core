<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Server\Traits\ServerEventTree;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;

/**
 * there was a phase edit action and the type was added into one or more places
 */
class LivePermissionAdded extends Evt\ScopeServer implements ICommandCallable
{
    use ServerEventTree;
    const UUID = '38af3121-482e-49d1-9264-717792331662';
    const EVENT_NAME = TypeOfEvent::LIVE_PERMISSION_ADDED;


    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];



    /** * @throws \Throwable */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'Live permission added~ ');
    }

}

