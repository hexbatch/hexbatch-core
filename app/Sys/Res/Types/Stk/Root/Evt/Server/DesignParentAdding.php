<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Enums\Types\TypeOfApproval;
use App\Helpers\Utilities;
use App\Models\ElementType;
use App\Models\ElementTypeParent;
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


class DesignParentAdding extends Evt\ScopeServer implements ICommandCallable
{
    const UUID = 'be5621ec-355d-48c4-a838-a3e0735fb3af';
    const EVENT_NAME = TypeOfEvent::DESIGN_PARENT_ADDING;


    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];


    public function __construct(
        protected ElementType   $given_type,
        protected ElementType   $parent_type,

    )
    {

    }

    protected  function toArray() :array {
        return [
            'given_type'=>$this->given_type,
            'parent_type'=>$this->parent_type,
        ];
    }

    protected static function fromArray(array $args) : static {
        $given_type = static::getTypeFromArray('given_type',$args);
        $parent_type = static::getTypeFromArray('parent_type',$args);

        return new static(given_type: $given_type,parent_type: $parent_type,);
    }

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called event design parent adding node");
        $work = static::fromArray($command_args);
        $did_pass = $work->doWork($children_args);
        $work->setParentStatus( $did_pass? TypeOfApproval::DESIGN_APPROVED: TypeOfApproval::DESIGN_DENIED);

        return new CallableReturnStub(status: $did_pass? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL, data: $children_args);
    }

    protected function doWork(array $children_args) : bool
    {
        //all children must agree
        foreach ($children_args as $key=>$val) {
            Utilities::ignoreVar($key);
            if (!$val) {

                return false;
            }
        }
        return true;
    }

    protected function setParentStatus(TypeOfApproval $approval_status) {
        ElementTypeParent::updateParentStatus(parent: $this->parent_type,child: $this->given_type,approval: $approval_status);
    }

    /**
     * @throws \Throwable
     */
    public static function callParentTree(
        ElementType $given_type,ElementType $parent_type,
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

        //ask the parent unless public domain
        if (!$parent_type->isPublicDomain()) {
            $parent_type->loadMissing('type_ancestors');
            $ancestors = $parent_type->type_ancestors;
            foreach ($ancestors as $ant) {
                if (!$ant->isPublicDomain() && ($ref = $ant->getEventHandlerRef(TypeOfEvent::DESIGN_PARENT_ADDING)))
                {
                    $builder->leaf(
                        command_class: Evt\EventHandler::class,
                        command_args: (array)new Evt\EventHandler(
                            ref: $ref,
                            type_context: $given_type
                        ),
                        command_tags: [Evt\EventHandler::class]
                    );
                }
            }
        }


        if ($ret_builder) {
            return $builder;
        }

        return  $builder->execute()->getThang();

    }
}

