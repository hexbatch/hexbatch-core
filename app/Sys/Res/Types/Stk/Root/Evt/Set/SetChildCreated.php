<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Helpers\Events\EventFilter;
use App\Helpers\Events\TreeStub;
use App\Models\ElementSet;
use App\Models\Server;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele\SetCreate;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Facades\Log;


class SetChildCreated extends Evt\ScopeSet implements ICommandCallable
{
    const UUID = '5db9e2bd-3175-45e8-87bc-67b05969d727';
    const EVENT_NAME = TypeOfEvent::SET_CHILD_CREATED;


    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

    public function __construct(
        protected ?ElementSet $created_set = null,
        protected ?ElementSet $parent_set = null,
    )
    {

    }

    protected  function toArray() :array {
        return [
            'created_set'=>$this->created_set->toArray(),
            'parent_set'=>$this->created_set->toArray(),
        ];
    }

    protected static function fromArray(array $args) : static {
        $created_set = static::getSetFromArray('created_set',$args,false );
        $parent_set = static::getSetFromArray('parent_set',$args, false);
        return new static(created_set: $created_set,parent_set: $parent_set);
    }


    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $did_pass = static::getDecisionUsingAndLogic($children_args);

        if ($did_pass) {
            $hat = static::fromArray($command_args);
            $hat->doWork($children_args); //call the notices out, it is ok if they are async or not
        }
        Log::debug("Called SetChildCreated node");

        return new CallableReturnStub(status: $did_pass? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL, data: $children_args);
    }

    /**
     * @throws \Throwable
     */
    protected function doWork(array $children_args) {
        $this->created_set = $children_args[SetCreate::SET_KEY_IN_ARGS]??null;
        if (!$this->created_set) {throw new \LogicException("Did not find set in event");}
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


        if ( $col = Server::getEventHandlerRefs(
            new EventFilter(event_type: TypeOfEvent::SET_CHILD_CREATED, type_context: $this->created_set->defining_type,
                element_context: $this->created_set->defining_element))
        ) {
            foreach ($col as $ref) {
                $builder->leaf(
                    command_class: Evt\EventHandler::class,
                    command_args: new Evt\EventHandler(
                        ref: $ref,
                        type_context: $this->created_set->defining_type,
                        parent_type_context: $this->parent_set->defining_type,
                        set_context: $this->created_set,
                        parent_set_context: $this->parent_set,
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

