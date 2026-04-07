<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
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
use Illuminate\Support\Facades\Log;


class TypePublishing extends Evt\ScopeServer implements ICommandCallable
{
    const UUID = 'f470d540-308c-4d88-8204-88a077480581';
    const EVENT_NAME = TypeOfEvent::TYPE_PUBLISHING;

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
        Log::debug("Called event type published node");
        $did_pass = static::getDecisionUsingAndLogic($children_args);

        return new CallableReturnStub(status: $did_pass? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL, data: [static::CHILD_DECISION_KEY=>$did_pass]);
    }



    /**
     * @throws \Throwable
     */
    public static function callEventsForApprovalInPublishing(
        ElementType $given_type,
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

        foreach ($given_type->type_parents as $ant) {
            if (($ref = $ant->parent_type->getEventHandlerRef(TypeOfEvent::TYPE_PUBLISHING)))
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

        foreach ($given_type->type_attributes as $att) {
            if ((!$att->attribute_parent) || ($att->attribute_parent->type_owner->ref_uuid === $given_type->ref_uuid) )
            {
                continue;
            }

            if (($ref = $att->attribute_parent->type_owner->getEventHandlerRef(TypeOfEvent::TYPE_PUBLISHING)))
            {
                $builder->leaf(
                    command_class: Evt\EventHandler::class,
                    command_args: (array)new Evt\EventHandler(
                        ref: $ref,
                        type_context: $given_type,
                        attribute_context: $att
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

