<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Models\Element;
use App\Models\ElementSet;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Data\Params\CommandParams;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Facades\Log;


class SetCreating extends Evt\ScopeType implements ICommandCallable
{
    const UUID = '26e8e548-cfe7-4d77-8d57-6ec164751a83';
    const EVENT_NAME = TypeOfEvent::SET_CREATING;

    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];

    public function __construct(
        protected Element $defining_element,
        protected ?ElementSet $parent_set = null
    )
    {

    }

    protected  function toArray() :array {
        return [
            'parent_set'=>$this->parent_set?->toArray(),
            'defining_element'=>$this->defining_element->toArray(),
        ];
    }

    protected static function fromArray(array $args) : static {
        $parent_set = static::getSetFromArray('parent_set',$args,false);
        $defining_element = static::getElementFromArray('defining_element',$args);
        return new static(defining_element: $defining_element,parent_set: $parent_set);
    }



    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $did_pass = static::getDecisionUsingAndLogic($children_args);
        Log::debug("Called SetCreating node");

        return new CallableReturnStub(status: $did_pass? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL, data: $children_args);
    }


    /**
     * @throws \Throwable
     */
    public static function callEventTree(
         Element $defining_element,
         ?ElementSet $parent_set = null,
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


        $defining_element->element_parent_type->loadMissing('type_ancestors');
        $parent_set?->defining_type?->loadMissing('type_ancestors');

        foreach ($defining_element->element_parent_type->getAllAncestorsAndMe() as $ancestor) {
            if (($ref = $ancestor->getEventHandlerRef(TypeOfEvent::SET_CREATING)))
            {
                $builder->leaf(
                    command_class: Evt\EventHandler::class,
                    command_args: new Evt\EventHandler(
                        ref: $ref,
                        type_context: $defining_element->element_parent_type,
                        other_type_context: $parent_set?->defining_type
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

