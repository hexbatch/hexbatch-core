<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Server\Traits\ServerEventTree;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;

/**
 * there was a phase edit action and the type was removed from one or more places
 */
class PhasePurged extends Evt\ScopeServer implements ICommandCallable
{
    use ServerEventTree;
    const UUID = '27aff549-4d9f-47a9-b7f9-769743928b2e';
    const EVENT_NAME = TypeOfEvent::PHASE_PURGED;


    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];


    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'Phase removed~ ');
    }

}

