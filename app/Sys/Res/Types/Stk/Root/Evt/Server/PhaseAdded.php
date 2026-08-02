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
class PhaseAdded extends Evt\ScopeServer implements ICommandCallable
{
    use ServerEventTree;
    const UUID = '21a14472-a759-4ea2-a193-63109de478ff';
    const EVENT_NAME = TypeOfEvent::PHASE_ADDED;


    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];



    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'Phase added~ ');
    }

}

