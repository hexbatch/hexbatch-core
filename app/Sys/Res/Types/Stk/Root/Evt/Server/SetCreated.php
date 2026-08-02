<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Helpers\Events\EventFilter;
use App\Models\Server;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele\SetCreate;
use App\Sys\Res\Types\Stk\Root\Evt;

use App\Sys\Res\Types\Stk\Root\Evt\Server\Traits\ServerEventTree;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Facades\Log;


class SetCreated extends Evt\ScopeServer implements ICommandCallable
{
    use ServerEventTree;
    const UUID = '21dcf822-13a1-4abd-a400-3c6b1e74b82b';
    const EVENT_NAME = TypeOfEvent::SET_CREATED;


    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

    /** * @throws \Throwable */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'Set created~ ');
    }


}

