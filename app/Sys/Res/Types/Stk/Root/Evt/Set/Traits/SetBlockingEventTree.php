<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set\Traits;

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


trait SetBlockingEventTree
{

    protected  function toArray() :array {
        return [
            'given_element'=>$this->given_element,
            'given_set'=>$this->given_set,
        ];
    }

    protected static function fromArray(array $args) : static {
        $given_element = static::getElementFromArray('given_element',$args);
        $given_set = static::getSetFromArray('given_set',$args);

        return new static(given_set: $given_set, given_element: $given_element);
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
                'given_set' => $work->given_set,
                'given_element' => $work->given_element
            ]
        );
    }




    /**
     * @throws \Throwable
     */
    protected  static function callEventTreeInner(
        ElementSet            $given_set,
        Element               $given_element,
        ?IThangBuilder $builder = null
    ) : Thang|IThangBuilder
    {

        /*
        * notifications can be either for the type of the element or the type of the definer in the set
        */

        $given_set->loadMissing('defining_type');
        $given_element->loadMissing('element_parent_type');

        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $builder?: $builder = ThangBuilder::createBuilder();

        $me = new static(given_set: $given_set,given_element: $given_element);
        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>[static::class],
            'command_args' => $me->toArray()
        ]);

        $builder->tree($my_command);


        $col = $given_set->defining_type->getEventHandlersFromTypeChain( static::EVENT_NAME);
        foreach ($col as $ref) {
            $builder->leaf(
                command_class: Evt\EventHandler::class,
                command_args: new Evt\EventHandler(
                    ref: $ref,
                    set_context: $given_set,
                    element_context: $given_element,
                )->toArray(),
                command_tags: [Evt\EventHandler::class]
            );
        }

        $col = $given_element->element_parent_type->getEventHandlersFromTypeChain( static::EVENT_NAME);
        foreach ($col as $ref) {
            $builder->leaf(
                command_class: Evt\EventHandler::class,
                command_args: new Evt\EventHandler(
                    ref: $ref,
                    set_context: $given_set,
                    element_context: $given_element,
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

