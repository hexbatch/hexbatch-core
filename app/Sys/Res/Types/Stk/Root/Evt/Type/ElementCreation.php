<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Models\ElementType;
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


class ElementCreation extends Evt\ScopeType implements ICommandCallable
{
    const UUID = '41d42dcb-2429-4183-82d5-7c3a04a36a1b';
    const EVENT_NAME = TypeOfEvent::ELEMENT_CREATION;

    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];

    public function __construct(
        protected ElementType $element_type,
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
            'element_type'=>$this->element_type->toArray(),
            'recipient_namespace'=>$this->recipient_namespace->toArray(),
        ];
    }

    protected static function fromArray(array $args) : static {
        $given_elements = static::getElementCollectionFromArray('given_elements',$args);
        $number_of_elements = $args['number_of_elements'];
        $element_type = static::getTypeFromArray('element_type',$args);
        $recipient_namespace = static::getNamespaceFromArray('recipient_namespace',$args);
        return new static(element_type: $element_type,recipient_namespace: $recipient_namespace,
            given_elements: $given_elements,number_of_elements: $number_of_elements);
    }



    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $did_pass = $children_args[static::CHILD_DECISION_KEY]??false;
        Log::debug("Called ElementCreation node");

        return new CallableReturnStub(status: $did_pass? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL, data: $children_args);
    }


    /**
     * @throws \Throwable
     */
    public static function callOwnerTree(
        ElementType $element_type,
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


        if ( ($ref = $element_type->getEventHandlerRef(TypeOfEvent::ELEMENT_CREATION)))
        {
            $builder->leaf(
                command_class: Evt\EventHandler::class,
                command_args: (array)new Evt\EventHandler(
                    ref: $ref,
                    type_context: $element_type,
                    namespace_context: $recipient_namespace,
                    collection_context: $given_elements,
                    important_value: $number_of_elements,
                ),
                command_tags: [Evt\EventHandler::class]
            );
        }

        $element_type->loadMissing('type_ancestors');
        $ancestors = $element_type->type_ancestors;
        foreach ($ancestors as $ant) {
            if ( ($ref = $ant->getEventHandlerRef(TypeOfEvent::DESIGN_PARENT_ADDING)))
            {
                $builder->leaf(
                    command_class: Evt\EventHandler::class,
                    command_args: (array)new Evt\EventHandler(
                        ref: $ref,
                        type_context: $element_type,
                        namespace_context: $recipient_namespace,
                        collection_context: $given_elements,
                        important_value: $number_of_elements
                    ),
                    command_tags: [Evt\EventHandler::class]
                );
            }
        }



        if ($ret_builder) {
            return $builder;
        }

        return  $builder->execute()->getThang();

    }

}

