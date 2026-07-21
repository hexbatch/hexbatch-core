<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Illuminate\Support\Facades\Log;


class TypeOwnerChanging extends Evt\ScopeServer implements ICommandCallable
{//todo change to have own tree
    const UUID = '6c6fb95e-b5cb-43d0-a6bd-1e2ad69593d8';
    const EVENT_NAME = TypeOfEvent::TYPE_OWNER_CHANGING;

    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];


    protected  function toArray() :array {
        return [
            'given_type'=>$this->given_type,
            'new_namespace'=>$this->given_namespace,
            'old_namespace'=>$this->old_namespace,
        ];
    }

    protected static function fromArray(array $args) : static {
        $given_type = static::getTypeFromArray('given_type',$args);
        $new_namespace = static::getNamespaceFromArray('new_namespace',$args) ;
        $old_namespace = static::getNamespaceFromArray('old_namespace',$args) ;

        return new static(given_type: $given_type, given_namespace: $new_namespace, old_namespace: $old_namespace);
    }

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called event owner changing node");
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $children_args);
    }

}

