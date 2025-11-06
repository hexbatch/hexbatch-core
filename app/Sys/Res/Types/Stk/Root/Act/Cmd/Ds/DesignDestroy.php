<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\Types\Params\TypeParamData;
use App\Enums\Sys\TypeOfAction;
use App\Models\ElementType;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Illuminate\Support\Facades\DB;


#[HexbatchTitle( title: "Destroy a design")]
#[HexbatchBlurb( blurb: "Designs can be deleted by type admins without any events")]
#[HexbatchDescription( description:'
# Destroy a design

    The admin group of a type can destroy it without any events raised.
    Can bypass check with flag


')]
class DesignDestroy extends Act\Cmd\Ds implements ICommandCallable
{ //this
    const UUID = 'd21d7294-35f8-4938-bff4-3e57ffe95e55';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_DESTROY;


    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

    #[ApiParamMarker( param_class: TypeParamData::class)]
    public function __construct(
        protected ElementType   $given_type,
        protected UserNamespace $caller_namespace,
        protected bool          $do_permission_check

    )
    {

    }

    protected  function toArray() :array {
        return [
            'given_type'=>$this->given_type,
            'do_permission_check'=>$this->do_permission_check,
            'namespace'=>$this->caller_namespace,
        ];
    }

    protected static function fromArray(array $args) : static {

        $given_type = static::getTypeFromArray('given_type',$args);
        $caller_namespace = static::getNamespaceFromArray('namespace',$args);
        $do_permission_check = $args['do_permission_check'];
        return new static(given_type: $given_type,caller_namespace: $caller_namespace,do_permission_check: $do_permission_check);
    }

    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $new_design = $work->deleteDesign();
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $new_design->toArray());
    }

    /**
     * @throws \Throwable
     */
    protected  function deleteDesign() : ElementType {

        if($this->do_permission_check) {
            static::checkIfGivenIsAdmin(given: $this->caller_namespace,target: $this->given_type->owner_namespace);
        }

        $this->given_type->checkInUse();
        try {
            DB::beginTransaction();
            $this->given_type->delete();
            DB::commit();
            return $this->given_type;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }



}

