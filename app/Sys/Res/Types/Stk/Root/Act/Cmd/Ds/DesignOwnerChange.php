<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\Types\Params\TypeOwnershipChangeParamData;
use App\Enums\Sys\TypeOfAction;
use App\Models\ElementType;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;


#[HexbatchTitle( title: "Change the ownership of a design")]
#[HexbatchBlurb( blurb: "Unpublished designs have their ownership changed here, this can be refused by the otherwise new owner")]
#[HexbatchDescription( description: /** @lang markdown */
    '

   # Design ownership changed

    A design can be given to some other namespace

    The future type owner will get an event, and the namespace group, to the type has start this


   * [TypeOwnerChanging](../../../Evt/Server/TypeOwnerChanging.php)

   if the new owner agrees, or does not have an event handler set, then the ownership is changed

   and the older and new type owners and type owners gets the following

   * [TypeOwnerChanged](../../../Evt/Server/TypeOwnerChanged.php)

   These checks and events
')]
#[ApiParamMarker( param_class: TypeOwnershipChangeParamData::class)]
class DesignOwnerChange extends Act\Cmd\Ds implements ICommandCallable
{
    const UUID = '3baa3285-5dff-42b5-bd22-071ad39101db';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_OWNER_CHANGE;

    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\TypeOwnerChanging::class,
        Evt\Server\TypeOwnerChanged::class
    ];


    #[ApiParamMarker( param_class: TypeOwnershipChangeParamData::class)]
    public function __construct(
        protected ElementType   $given_type,
        protected UserNamespace $given_namespace,
        protected UserNamespace $caller_namespace,
        protected bool          $do_permission_check

    )
    {

    }

    protected  function toArray() :array {
        return [
            'given_type'=>$this->given_type,
            'given_namespace'=>$this->given_namespace,
            'do_permission_check'=>$this->do_permission_check,
            'caller_namespace'=>$this->caller_namespace,
        ];
    }

    protected static function fromArray(array $args) : static {
        $given_type = $args['given_type'];
        $given_namespace = static::getNamespaceFromArray('given_namespace',$args);
        $caller_namespace = static::getNamespaceFromArray('caller_namespace',$args);
        $do_permission_check = $args['do_permission_check'];
        return new static(given_type: $given_type,given_namespace: $given_namespace,
            caller_namespace: $caller_namespace,do_permission_check: $do_permission_check);
    }

    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $new_design = $work->changeOwner();
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $new_design->toArray());
    }

    /**
     * @throws \Throwable
     */
    protected  function changeOwner() : ElementType {

        if ($this->do_permission_check) {
            static::checkIfGivenIsAdmin(given: $this->caller_namespace,target: $this->given_type->owner_namespace);
        }

        $this->given_type->owner_namespace_id = $this->caller_namespace->id ;
        $this->given_type->save();
        return $this->given_type;
    }

}

