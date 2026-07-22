<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;


use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\Types\Params\TypeParamData;
use App\Enums\Attributes\TypeOfServerAccess;
use App\Enums\Sys\TypeOfAction;
use App\Helpers\Utilities;
use App\Models\Element;
use App\Models\ElementType;
use App\Models\ElementTypeParent;
use App\Models\ElementTypeServerLevel;
use App\Models\Server;
use App\Models\TimeBound;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Illuminate\Support\Facades\DB;


#[HexbatchTitle( title: "Design create")]
#[HexbatchBlurb( blurb: "Types are created here")]
#[HexbatchDescription( description: "
## Types can be set with the following properties

* the owning namespace
* is_system : code can set if this is a system, api cannot
* type_name: has to be unique in the namespace
* schedule: types can have a schedule
* is_final: cannot be a parent
* access: sets access for this server
* handle: set the element handle

")]

class DesignCreate extends Act\Cmd\Ds implements ICommandCallable
{
    const UUID = 'f635c4b8-5903-4688-802c-c0b28f376be0';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_CREATE;


    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];




    #[ApiParamMarker( param_class: TypeParamData::class)]
    public function __construct(
        protected TypeParamData     $params,
        protected bool              $is_system,
        protected ?string              $use_ref,
        protected ?UserNamespace      $owner_namespace,
        protected Server            $server

    )
    {

    }

    protected  function toArray() :array {
        return [
            'design_params'=>$this->params->toArray(),
            'use_ref'=>$this->use_ref,
            'is_system'=>$this->is_system,
            'namespace'=>$this->owner_namespace,
            'server'=>$this->server,
        ];
    }
    protected static function fromArray(array $args) : static{
        $params = TypeParamData::from($args['design_params']);
        $is_system = (bool)$args['is_system'];
        $use_ref = $args['use_ref'];
        $owner_namespace = static::getNamespaceFromArray('namespace',$args,false);
        $server = static::getServerFromArray('server',$args);
        return new DesignCreate(params: $params,is_system: $is_system,use_ref: $use_ref,owner_namespace: $owner_namespace,server: $server);
    }

    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $new_design = $work->createDesign();
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $new_design->toArray());
    }

    /**
     * @throws \Throwable
     */
    public  function createDesign() : ElementType {
        if ($this->use_ref) {
            if (!Utilities::is_uuid($this->use_ref)) {
                throw new \LogicException("Type use ref is not uuid ". $this->use_ref);
            }
        }
        try {
            DB::beginTransaction();

            $type = new ElementType();
            $type->setTypeName(name: $this->params->type_name,namespace: $this->owner_namespace,b_do_check: !!$this->owner_namespace);
            if ($this->params->schedule_ref_uuid) {
                $type->type_time_bound_id = TimeBound::getThisSchedule($this->params->schedule_ref_uuid)->id;
            }
            if ($this->use_ref) {
                $type->ref_uuid = $this->use_ref;
            }

            $type->owner_namespace_id = $this->owner_namespace?->id;
            $type->imported_from_server_id = $this->server->id;


            $type->is_system = $this->is_system;
            $type->is_final_type = $this->params->is_final_type??false;
            if ($this->params->handle_ref_uuid) {
                $type->type_handle_element_id = Element::getThisElement(uuid: $this->params->handle_ref_uuid)->id;
            }

            $type->save();

            $access = new ElementTypeServerLevel();
            $access->server_access_type_id = $type->id;
            $access->to_server_id = $type->imported_from_server_id;
            $access->access_type = $this->params->access??TypeOfServerAccess::IS_PRIVATE;
            $access->save();

            $root = ElementType::getSystemType();
            if ($root->id !== $type->id) {
                ElementTypeParent::addOrUpdateParent(parent: $root, child: $type);
            }


            DB::commit();
            return $type;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

