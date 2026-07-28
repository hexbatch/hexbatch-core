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
 * This goes to the type  when a single element of a type is changed ownership or given for the first time
 */
class ElementOwnerChange extends Evt\ScopeType implements ICommandCallable
{
    const UUID = 'c43da607-84d1-40f5-992d-db4d091e6ec9';
    const EVENT_NAME = TypeOfEvent::ELEMENT_OWNER_CHANGE;

    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];

    public function __construct(
        protected UserNamespace $recipient_namespace,
        protected Collection  $given_elements,
        protected int  $number_of_elements
    )
    {

    }

    protected  function toArray() :array {
        return [
            'given_elements'=>$this->given_elements->toArray(),
            'number_of_elements'=>$this->number_of_elements,
            'recipient_namespace'=>$this->recipient_namespace->toArray(),
        ];
    }

    protected static function fromArray(array $args) : static {
        $given_elements = static::getElementCollectionFromArray('given_elements',$args);
        $number_of_elements = $args['number_of_elements'];
        $recipient_namespace = static::getNamespaceFromArray('recipient_namespace',$args);
        return new static(recipient_namespace: $recipient_namespace,
            given_elements: $given_elements,number_of_elements: $number_of_elements);
    }



    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $did_pass = static::getDecisionUsingAndLogic($children_args);
        Log::debug("Called ElementOwnerChange node");

        return new CallableReturnStub(status: $did_pass? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL, data: $children_args);
    }



    /**
     * @throws \Throwable
     */
    public static function callOwnerTree(

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


        $ref = $recipient_namespace->namespace_base_type->getEventHandlerRef(TypeOfEvent::ELEMENT_OWNER_CHANGE);
        if ($ref)
        {
            $builder->leaf(
                command_class: Evt\EventHandler::class,
                command_args: new Evt\EventHandler(
                    ref: $ref,
                    namespace_context: $recipient_namespace,
                    collection_context: $given_elements,
                    important_value: $number_of_elements
                )->toArray(),
                command_tags: [Evt\EventHandler::class]
            );
        }



        if ($ret_builder) {
            return $builder;
        }

        return  $builder->execute()->getThang();

    }

}

