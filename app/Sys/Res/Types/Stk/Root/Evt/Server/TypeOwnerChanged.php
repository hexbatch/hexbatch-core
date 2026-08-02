<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Server\Traits\ServerEventTree;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Illuminate\Support\Facades\Log;


class TypeOwnerChanged extends Evt\ScopeServer implements ICommandCallable
{

    use ServerEventTree;
    const UUID = '451b9426-15cc-48f7-92f0-e361b5a0ab2a';
    const EVENT_NAME = TypeOfEvent::TYPE_OWNER_CHANGED;

    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

    /** * @throws \Throwable */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'Type owner changed~ ');
    }




}

