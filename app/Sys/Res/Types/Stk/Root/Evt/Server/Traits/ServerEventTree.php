<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server\Traits;

use App\Helpers\Events\EventFilter;
use App\Helpers\Events\TreeStub;
use App\Helpers\Utilities;
use App\Models\Attribute;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\Phase;
use App\Models\Server;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Facades\Log;


trait ServerEventTree
{


    public function __construct(
        protected ?ElementType   $given_type = null  ,
        protected ?ElementType   $other_type =null ,
        protected ?UserNamespace $given_namespace = null,
        protected ?UserNamespace $old_namespace = null,
        protected ?Element       $given_element = null,
        protected ?ElementSet    $given_set = null,
        protected ?Phase         $given_phase = null ,
        protected ?Attribute     $given_attribute = null,
        protected ?string        $given_uuid = null,

    )
    {

    }

    protected  function toArray() :array {
        return [
            'given_type'=>$this->given_type,
            'other_type'=>$this->other_type,
            'given_namespace'=>$this->given_namespace,
            'old_namespace'=>$this->old_namespace,
            'given_element'=>$this->given_element,
            'given_set'=>$this->given_set,
            'given_phase'=>$this->given_phase,
            'given_attribute'=>$this->given_attribute,
            'given_uuid'=>$this->given_uuid,
        ];
    }

    protected static function fromArray(array $args) : static {
        $given_type = static::getTypeFromArray('given_type',$args,false);
        $other_type = static::getTypeFromArray('other_type',$args);
        $given_namespace = static::getNamespaceFromArray('given_namespace',$args,throw_if_missing: false);
        $old_namespace = static::getNamespaceFromArray('old_namespace',$args,throw_if_missing: false);
        $given_element = static::getElementFromArray('given_element',$args,throw_if_missing: false);
        $given_set = static::getSetFromArray('given_set',$args,throw_if_missing: false);
        $given_phase = static::getSetFromArray('given_phase',$args,throw_if_missing: false);
        $given_attribute = static::getSetFromArray('given_attribute',$args,throw_if_missing: false);
        $given_uuid = $args['given_uuid']??null ;

        return new static(given_type: $given_type, other_type:$other_type,given_namespace: $given_namespace,
            old_namespace: $old_namespace,given_element: $given_element,given_set: $given_set,given_phase: $given_phase,
            given_attribute: $given_attribute,given_uuid: $given_uuid);
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

    protected function decide() : bool {
        return true;
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



        $builder?: $builder = ThangBuilder::createBuilder();
        $builder->tree(
            command_class: TreeStub::class,
            command_tags: [TreeStub::class,static::class]
        );


        if ($this->given_type) {
            $col = $this->given_type->getEventHandlersFromTypeChain( static::EVENT_NAME);
            foreach ($col as $ref) {
                $builder->leaf(
                    command_class: Evt\EventHandler::class,
                    command_args: new Evt\EventHandler(
                        ref: $ref,
                        type_context: $this->given_type,
                        other_type_context: $this->other_type,
                        namespace_context: $this->given_namespace,
                        old_namespace_context: $this->old_namespace,
                        attribute_context: $this->given_attribute,
                        set_context: $this->given_set,
                        element_context: $this->given_element,
                        phase_context: $this->given_phase,
                        important_array: $children_args
                    )->toArray(),
                    command_tags: [Evt\EventHandler::class]
                );
            }
        }


        if ( $col = Server::getEventHandlerRefs(
            new EventFilter(event_type: static::EVENT_NAME,
                type_context: $this->given_type,
                namespace_context: $this->given_namespace, old_namespace_context: $this->given_namespace,
                attribute_context: $this->given_attribute, set_context: $this->given_set, element_context: $this->given_element, phase_context: $this->given_phase))
        ) {
            foreach ($col as $ref) {
                $builder->tree(
                    command_class: Evt\EventHandler::class,
                    command_args: new Evt\EventHandler(
                        ref: $ref,
                        type_context: $this->given_type,
                        namespace_context: $this->given_namespace,
                        old_namespace_context: $this->old_namespace,
                        attribute_context: $this->given_attribute,
                        set_context: $this->given_set,
                        element_context: $this->given_element,
                        phase_context: $this->given_phase
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

    /**
     * @throws \Throwable
     */
    public static function makeEventTree(
        array          $children_args = [],
        ?ElementType   $given_type = null, ?ElementType $other_type = null, ?UserNamespace $given_namespace = null,
        ?UserNamespace $old_namespace = null, ?Element $given_element = null, ?ElementSet $given_set = null, ?Phase $given_phase = null,
        ?Attribute     $given_attribute = null, ?string $given_uuid = null, ?IThangBuilder $builder = null)
    : Thang|IThangBuilder|null
    {
        $r = new static(given_type: $given_type, other_type: $other_type,given_namespace: $given_namespace,old_namespace: $old_namespace,given_element: $given_element,
            given_set: $given_set,given_phase: $given_phase,given_attribute: $given_attribute,
            given_uuid: $given_uuid);
        return $r->callEventTreeInner($children_args,$builder);
    }

}

