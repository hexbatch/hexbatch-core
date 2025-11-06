<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element\Traits;

use App\Helpers\Events\TreeStub;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Facades\Log;


trait ElementNotificationEventTree
{

    protected  function toArray() :array {
        return [
            'given_element'=>$this->given_element,
            'given_set'=>$this->given_set,
        ];
    }

    protected static function fromArray(array $args) : static {
        $given_element = static::getElementFromArray('given_element',$args);
        $given_set = static::getSetFromArray('given_set',$args,throw_if_missing: false);

        return new static(given_element: $given_element,given_set: $given_set);
    }

    /**
     * @throws \Throwable
     */
    protected function doCallInner(array $children_args, array $command_args, string $logged_name): ICmdCallReturn
    {
        $did_pass = static::getDecisionUsingAndLogic($children_args);

        if ($did_pass) {
            $hat = static::fromArray($command_args);
            $hat->callEventTreeInner($children_args);
            //call the notices out, it is ok if they are async or not
        }
        Log::debug("Called $logged_name node");

        return new CallableReturnStub(status: $did_pass? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL, data: $children_args);
    }

    /**
     * @throws \Throwable
     */
    public function callTreeByItself(array $children_args) : Thang {
        return $this->callEventTreeInner($children_args);
    }


    /**
     * @throws \Throwable
     */
    protected function callEventTreeInner(
        array             $children_args,
        ?IThangBuilder $builder = null
    ) : Thang|IThangBuilder
    {
        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }
        /*
         * notifications can be either for the type of the element or the type of the definer in the set,
         * or any types in the set elements
         */
        $this->given_element->loadMissing('element_parent_type');


        $builder?: $builder = ThangBuilder::createBuilder();
        $builder->tree(
            command_class: TreeStub::class,
            command_tags: [TreeStub::class,static::class]
        );


        $col = $this->given_element->element_parent_type->getEventHandlersFromTypeChain( static::EVENT_NAME);
        foreach ($col as $ref) {
            $builder->leaf(
                command_class: Evt\EventHandler::class,
                command_args: new Evt\EventHandler(
                    ref: $ref,
                    type_context: $this->given_element->element_parent_type,
                    set_context: $this->given_set,
                    element_context: $this->given_element,
                    important_array: $children_args
                )->toArray(),
                command_tags: [Evt\EventHandler::class]
            );
        }

        $col = $this->given_element->element_namespace->getEventHandlersFromNamespace( static::EVENT_NAME);
        foreach ($col as $ref) {
            $builder->leaf(
                command_class: Evt\EventHandler::class,
                command_args: new Evt\EventHandler(
                    ref: $ref,
                    type_context: $this->given_element->element_parent_type,
                    set_context: $this->given_set,
                    element_context: $this->given_element,
                    important_array: $children_args
                )->toArray(),
                command_tags: [Evt\EventHandler::class]
            );
        }

        if ($this->given_set) {
            $this->given_set->loadMissing('defining_type');

            $col = $this->given_set->defining_type->getEventHandlersFromTypeChain( static::EVENT_NAME);
            foreach ($col as $ref) {
                $builder->leaf(
                    command_class: Evt\EventHandler::class,
                    command_args: new Evt\EventHandler(
                        ref: $ref,
                        type_context: $this->given_element->element_parent_type,
                        set_context: $this->given_set,
                        element_context: $this->given_element,
                        important_array: $children_args
                    )->toArray(),
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

