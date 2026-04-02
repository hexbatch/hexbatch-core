<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Helpers\Utilities;
use App\Models\Element;
use App\Models\ElementType;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Fired on the private namespace element when a single element of a type is given to the user
 * if ns assigned when an element is created, the element creation fails if this rejects
 */
class ElementRecieved extends Evt\ScopeElement implements ICommandCallable
{
    const UUID = 'd9475a78-0c0b-46d6-920c-5ebcd2159f7c';
    const EVENT_NAME = TypeOfEvent::ELEMENT_RECIEVED;


    const PARENT_CLASSES = [
        Evt\ScopeElement::class
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
        Log::debug("Called ElementRecieved node");
        $work = static::fromArray($command_args);
        $did_pass = $work->doWork($children_args);

        return new CallableReturnStub(status: $did_pass? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL, data: [static::CHILD_DECISION_KEY]);
    }

    protected function doWork(array $children_args) : bool
    {
        Utilities::ignoreVar($children_args);
        return true;
    }

}

