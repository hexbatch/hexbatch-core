<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Data\Params\CommandParams;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
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
        protected Collection  $given_elements
    )
    {

    }

    protected  function toArray() :array {
        return [
            'given_elements'=>$this->given_elements->toArray(),
        ];
    }

    protected static function fromArray(array $args) : static {
        $given_elements = static::getElementCollectionFromArray('given_elements',$args);
        return new static(given_elements: $given_elements);
    }



    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called ElementRecieved node");
        $did_pass = static::getDecisionUsingAndLogic($children_args);

        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS, data: [static::CHILD_DECISION_KEY=>$did_pass]);
    }




    /**
     * @throws \Throwable
     */
    public static function callRecievedTree(
        Collection  $given_elements,
        UserNamespace $recipient_namespace,
        int  $number_of_elements,
        ?IThangBuilder $builder = null
    ) : Thang|IThangBuilder
    {
        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $builder?: $builder = ThangBuilder::createBuilder();

        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>[static::class]
        ]);
        $builder->tree($my_command);


        if ( ($ref = $recipient_namespace->namespace_base_type->getEventHandlerRef(static::EVENT_NAME)))
        {
            $builder->leaf(
                command_class: Evt\EventHandler::class,
                command_args: (array)new Evt\EventHandler(
                    ref: $ref,
                    namespace_context: $recipient_namespace,
                    collection_context: $given_elements,
                    important_value: $number_of_elements,
                ),
                command_tags: [Evt\EventHandler::class]
            );
        }

        if ($ret_builder) {
            return $builder;
        }

        return  $builder->execute()->getThang();

    }

}

