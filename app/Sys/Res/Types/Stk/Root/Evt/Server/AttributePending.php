<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Illuminate\Support\Facades\Log;


class AttributePending extends Evt\ScopeServer implements ICommandCallable
{
    const UUID = 'cc9de75b-2bf7-4cd2-b2a8-9567f10a8747';
    const EVENT_NAME = TypeOfEvent::ATTRIBUTE_PENDING;




    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        //todo if not any parent here do not do anything
        Log::debug("Called event attribute pending node");
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $children_args);
    }
}

