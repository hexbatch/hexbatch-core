<?php

namespace App\Helpers\Events;

use App\Sys\Res\Types\BaseType;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Illuminate\Support\Facades\Log;

class TreeStub implements ICommandCallable
{

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $did_pass = BaseType::getDecisionUsingAndLogic($children_args);
        Log::debug("Called TreeStub node");
        return new CallableReturnStub(status: $did_pass? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL, data: $children_args);
    }
}
