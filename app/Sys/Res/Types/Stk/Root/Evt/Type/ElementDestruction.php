<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Helpers\Utilities;
use App\Models\Element;
use App\Models\ElementType;
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


class ElementDestruction extends Evt\ScopeType implements ICommandCallable
{
    const UUID = '2c1cb906-04a6-4f7c-aceb-abd9f9598ad7';
    const EVENT_NAME = TypeOfEvent::ELEMENT_DESTRUCTION;

    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];


    public function __construct(
        /** @param Collection<Element> $elements */
        protected Collection $elements
    )
    {

    }

    protected  function toArray() :array {
        return [
            'elements'=>$this->elements->toArray(),
        ];
    }

    protected static function fromArray(array $args) : static {
        $elements = static::getElementCollectionFromArray('elements',$args );
        return new static(elements: $elements);
    }


    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Utilities::ignoreVar($command_args);
        $did_pass = static::getDecisionUsingAndLogic($children_args);

        Log::debug("Called ElementDestuction node");

        return new CallableReturnStub(status: $did_pass? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL, data: $children_args);
    }




    /**
     * @param Collection<Element> $elements
     * @throws \Throwable
     */
    public static  function callEventTree(
        Collection $elements,
        ?IThangBuilder $builder = null
    ) : Thang|IThangBuilder
    {
        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $builder?: $builder = ThangBuilder::createBuilder();

        $me = new static(elements: $elements);

        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_args' => $me->toArray(),
            'command_tags' =>[static::class]
        ]);
        $builder->tree($my_command);



        $types = [];
        foreach ($elements as $el) {
            if (!array_key_exists($el->element_parent_type->ref_uuid,$types)) {
                $types[$el->element_parent_type->ref_uuid] = ['type'=>$el->element_parent_type,'elements'=> new Collection];
            }
            $types[$el->element_parent_type->ref_uuid]['elements']->add($el);
        }

        foreach ($elements as $el) {
            $types[] = $el->element_namespace->namespace_base_type;

            if (!array_key_exists($el->element_namespace->namespace_base_type->ref_uuid,$types)) {
                $types[$el->element_namespace->namespace_base_type->ref_uuid] =
                    ['type'=>$el->element_namespace->namespace_base_type,'elements'=> new Collection];
            }
            $types[$el->element_namespace->namespace_base_type->ref_uuid]['elements']->add($el);
        }
        foreach ($types as  $arr) {
            /** @var ElementType $type */
            $type = $arr['type'];

            /** @var Collection<Element> $col */
            $els = $arr['elements'];
            if($els->isEmpty()) {continue;}

            $col = $type->getEventHandlersFromTypeChain( TypeOfEvent::ELEMENT_DESTROYED);
            foreach ($col as $ref) {
                $builder->leaf(
                    command_class: Evt\EventHandler::class,
                    command_args: (array)new Evt\EventHandler(
                        ref: $ref,
                        type_context: $type,
                        collection_context: $els
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

