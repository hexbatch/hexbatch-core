<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Models\Attribute;
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


class AttributePending extends Evt\ScopeServer implements ICommandCallable
{
    const UUID = 'cc9de75b-2bf7-4cd2-b2a8-9567f10a8747';
    const EVENT_NAME = TypeOfEvent::ATTRIBUTE_PENDING;




    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

    public function __construct(
        protected Attribute            $ancestor_attribute,
        protected Attribute             $given_attribute

    )
    {

    }

    protected  function toArray() :array {
        return [
            'ancestor_attribute'=>$this->ancestor_attribute,
            'given_attribute'=>$this->given_attribute,
        ];
    }

    protected static function fromArray(array $args) : static {
        $ancestor_attribute = static::getAttributeFromArray('ancestor_attribute',$args);
        $given_attribute = static::getAttributeFromArray('given_attribute',$args);

        return new static(ancestor_attribute: $ancestor_attribute,given_attribute: $given_attribute);
    }

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {

        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        if ($b_approved) {
            $b_approved = $work->decide();
        }
        Log::debug("Called event attribute pending node");
        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,data:
                [
                    'children'=> $children_args,
                    'attribute' => $work->given_attribute,
                    'parent' => $work->ancestor_attribute
                ]
            );
    }

    protected function decide() : bool {
        return true;
    }

    /**
     * @throws \Throwable
     */
    public static function callParentTree(
        Attribute            $ancestor_attribute,
        Attribute             $given_attribute,
        ?IThangBuilder $builder = null
    ) : Thang|IThangBuilder|null
    {
        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $ancestor_attribute->loadMissing('attribute_parent');
        $ancestor_attribute->loadMissing('type_owner');
        if ($ancestor_attribute->type_owner->isPublicDomain()) { return $builder; }

        $builder?: $builder = ThangBuilder::createBuilder();

        $me = new static(ancestor_attribute: $ancestor_attribute,given_attribute: $given_attribute);

        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>[static::class],
            'command_args' => $me->toArray()
        ]);
        $builder->tree($my_command);

        if (($ref = $ancestor_attribute->type_owner->getEventHandlerRef(TypeOfEvent::ATTRIBUTE_PENDING)))
        {
            $builder->leaf(
                command_class: Evt\EventHandler::class,
                command_args: (array)new Evt\EventHandler(
                    ref: $ref,
                    attribute_context: $given_attribute
                ),
                command_tags: [Evt\EventHandler::class]
            );
        }
        $dad = $ancestor_attribute->attribute_parent;
        static::callParentTree(ancestor_attribute: $dad,given_attribute: $given_attribute,builder: $builder);



        if ($ret_builder) {
            return $builder;
        }

        return  $builder->execute()->getThang();

    }
}

