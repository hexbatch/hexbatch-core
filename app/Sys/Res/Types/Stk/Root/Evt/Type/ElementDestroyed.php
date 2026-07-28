<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Helpers\Events\TreeStub;
use App\Models\Element;
use App\Models\ElementType;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;


class ElementDestroyed extends Evt\ScopeType implements ICommandCallable
{
    const UUID = 'a08204d3-b36b-44e9-a545-288d7da1bbd2';
    const EVENT_NAME = TypeOfEvent::ELEMENT_DESTROYED;

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
        $did_pass = static::getDecisionUsingAndLogic($children_args);

        if ($did_pass) {
            $hat = static::fromArray($command_args);
            $hat->doWork();
        }
        Log::debug("Called ElementDestroyed node");

        return new CallableReturnStub(status: $did_pass? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL, data: $children_args);
    }

    /**
     * @throws \Throwable
     */
    protected function doWork() {
        $this->callEventTree();
    }


    /**
     * @throws \Throwable
     */
    protected  function callEventTree(
        ?IThangBuilder $builder = null
    ) : Thang|IThangBuilder
    {
        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $builder?: $builder = ThangBuilder::createBuilder();

        $builder->tree(
            command_class: TreeStub::class,
            command_tags: [TreeStub::class,static::class]
        );


        $types = [];
        foreach ($this->elements as $el) {
            if (!array_key_exists($el->element_parent_type->ref_uuid,$types)) {
                $types[$el->element_parent_type->ref_uuid] = ['type'=>$el->element_parent_type,'elements'=> new Collection];
            }
            $types[$el->element_parent_type->ref_uuid]['elements']->add($el);
        }

        foreach ($this->elements as $el) {
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
                    command_args: new Evt\EventHandler(
                        ref: $ref,
                        type_context: $type,
                        collection_context: $els
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

