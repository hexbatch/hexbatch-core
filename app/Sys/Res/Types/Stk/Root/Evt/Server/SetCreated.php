<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Helpers\Events\EventFilter;
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


class SetCreated extends Evt\ScopeServer implements ICommandCallable
{
    const UUID = '21dcf822-13a1-4abd-a400-3c6b1e74b82b';
    const EVENT_NAME = TypeOfEvent::SET_CREATED;


    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];



    protected  function toArray() :array {
        return [
            'created_set'=>$this->given_set?->toArray(),
        ];
    }

    protected static function fromArray(array $args) : static {
        $set = static::getSetFromArray('created_set',$args,false);
        return new static(given_set: $set);
    }


    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $did_pass = static::getDecisionUsingAndLogic($children_args);
        if ($did_pass) {
            $work = static::fromArray($command_args);
            $work->doWork($children_args);
        }
        Log::debug("Called SetCreated node");

        return new CallableReturnStub(status: $did_pass? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL, data: $children_args);
    }

    /**
     * @throws \Throwable
     */
    protected function doWork(array $children_args) {
        $this->given_set = $children_args[SetCreate::SET_KEY_IN_ARGS]??null;
        if (!$this->given_set) {throw new \LogicException("Did not find set in event");}
        $this->callEventTree();
    }


    /**
     * @throws \Throwable
     */
    protected function callEventTree(
        ?IThangBuilder $builder = null
    ) : Thang|IThangBuilder
    {
        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $builder?: $builder = ThangBuilder::createBuilder();



        if ( $col = Server::getEventHandlerRefs(
            new EventFilter(event_type: TypeOfEvent::SET_CREATED, type_context: $this->given_set->defining_type, element_context: $this->given_set->defining_element))
        ) {
            foreach ($col as $ref) {
                $builder->tree(
                    command_class: Evt\EventHandler::class,
                    command_args: (array)new Evt\EventHandler(
                        ref: $ref,
                        set_context: $this->given_set,
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

