<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element\Traits;

use App\Models\Element;
use App\Models\ElementSet;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Data\Params\CommandParams;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Facades\Log;


trait ElementBlockingEventTree
{

    /**
     * @throws \Throwable
     */
    public static function callEventTree(
        Element               $given_element,
        ?ElementSet             $given_set,
        ?IThangBuilder $builder = null
    ) : Thang|IThangBuilder|null
    {
        return static::callEventTreeInner(given_element: $given_element,given_set: $given_set,builder: $builder);
    }

    protected  function toArray() :array {
        return [
            'given_element'=>$this->given_element,
        ];
    }

    protected static function fromArray(array $args) : static {
        $given_element = static::getElementFromArray('given_element',$args);

        return new static(given_element: $given_element);
    }

    protected function doCallInner(array $children_args, array $command_args,string $logged_name): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        if ($b_approved) {
            $b_approved = $work->decide();
        }
        Log::debug("Called event $logged_name node");
        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,data:
            [
                'children'=> $children_args,
                'given_element' => $work->given_element
            ]
        );
    }





    /**
     * @throws \Throwable
     */
    protected  static function callEventTreeInner(
        Element               $given_element,
        ?ElementSet           $given_set,
        ?IThangBuilder $builder = null,
        bool $b_ask_set = false
    ) : Thang|IThangBuilder
    {

        /*
        * notifications can be either for the type of the element or the type of the definer in the set
        */

        $given_element->loadMissing('element_parent_type');

        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $builder?: $builder = ThangBuilder::createBuilder();

        $me = new static(given_element: $given_element);
        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>[static::class],
            'command_args' => $me->toArray()
        ]);

        $builder->tree($my_command);

        $col = $given_element->element_parent_type->getEventHandlersFromTypeChain( static::EVENT_NAME);
        foreach ($col as $ref) {
            $builder->leaf(
                command_class: Evt\EventHandler::class,
                command_args: new Evt\EventHandler(
                    ref: $ref,
                    type_context: $given_element->element_parent_type,
                    set_context: $given_set,
                    element_context: $given_element,
                )->toArray(),
                command_tags: [Evt\EventHandler::class]
            );
        }

        if ($b_ask_set && $me->given_set) {
            $me->given_set->loadMissing('defining_type');

            $col = $me->given_set->defining_type->getEventHandlersFromTypeChain( static::EVENT_NAME);
            foreach ($col as $ref) {
                $builder->leaf(
                    command_class: Evt\EventHandler::class,
                    command_args: new Evt\EventHandler(
                        ref: $ref,
                        type_context: $me->given_element->element_parent_type,
                        set_context: $me->given_set,
                        element_context: $me->given_element
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

