<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server\Traits;

use App\Helpers\Events\TreeStub;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Facades\Log;


trait ServerNotificationEventTree
{

    protected  function toArray() :array {
        return [
            'given_type'=>$this->given_type,
            'given_namespace'=>$this->given_namespace,
        ];
    }

    protected static function fromArray(array $args) : static {
        $given_type = static::getElementFromArray('given_type',$args);
        $given_namespace = static::getSetFromArray('given_namespace',$args,throw_if_missing: false);

        return new static(given_type: $given_type,given_namespace: $given_namespace);
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



        $builder?: $builder = ThangBuilder::createBuilder();
        $builder->tree(
            command_class: TreeStub::class,
            command_tags: [TreeStub::class,static::class]
        );


        $col = $this->given_type->getEventHandlersFromTypeChain( static::EVENT_NAME);
        foreach ($col as $ref) {
            $builder->leaf(
                command_class: Evt\EventHandler::class,
                command_args: (array)new Evt\EventHandler(
                    ref: $ref,
                    type_context: $this->given_type,
                    namespace_context: $this->given_namespace
                ),
                command_tags: [Evt\EventHandler::class]
            );
        }


        if ($ret_builder) {
            return $builder;
        }

        return  $builder->execute()->getThang();

    }
}

