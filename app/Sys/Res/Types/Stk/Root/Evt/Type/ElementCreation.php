<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Helpers\Utilities;
use App\Models\ElementType;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;


class ElementCreation extends Evt\ScopeType implements ICommandCallable
{
    const UUID = '41d42dcb-2429-4183-82d5-7c3a04a36a1b';
    const EVENT_NAME = TypeOfEvent::ELEMENT_CREATION;

    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];

    public function __construct(
        protected ElementType $type_of_elements,
        protected Collection  $given_elements
    )
    {

    }

    protected  function toArray() :array {
        return [
            'given_elements'=>$this->given_elements->toArray(),
            'type_of_elements'=>$this->type_of_elements,
        ];
    }

    protected static function fromArray(array $args) : static {
        $given_elements = static::getElementCollectionFromArray('given_elements',$args);
        $type_of_elements = static::getTypeFromArray('type_of_elements',$args);
        return new static(type_of_elements: $type_of_elements,given_elements: $given_elements);
    }



    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called ElementCreation node");
        $work = static::fromArray($command_args);
        $did_pass = $work->doWork($children_args);

        return new CallableReturnStub(status: $did_pass? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL, data: [static::CHILD_DECISION_KEY]);
    }

    protected function doWork(array $children_args) : bool
    {
        //all children must agree
        foreach ($children_args as $key=>$val) {
            Utilities::ignoreVar($key);
            if (!$val) { return false;}
        }
        return true;
    }

}

