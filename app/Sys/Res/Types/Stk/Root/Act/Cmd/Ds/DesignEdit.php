<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\Types\Params\TypeParamData;
use App\Enums\Sys\TypeOfAction;
use App\Models\Element;
use App\Models\ElementType;
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


#[HexbatchTitle( title: "Designs can be edited")]
#[HexbatchBlurb( blurb: "If approving parents, they can review when this is published")]
#[HexbatchDescription( description:'
# Editing a type

The only time a type is editable is in design mode.

* server : code can set the server
* type_name: has to be unique in the namespace
* schedule: types can have a schedule
* is_final: cannot be a parent
* access: sets access for this server
* handle: set the element handle

')]

class DesignEdit extends Act\Cmd\Ds implements ICommandCallable
{
    const UUID = '9f0285dc-0af5-4176-b82d-ac930d93b132';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_EDIT;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

    #[ApiParamMarker( param_class: TypeParamData::class)]
    public function __construct(
        protected ElementType $given_type,
        protected TypeParamData $params,
        protected UserNamespace $caller_namespace,
        protected ?Server        $server = null

    )
    {

    }

    protected  function toArray() :array {
        return [
            'given_type'=>$this->given_type,
            'design_params'=>$this->params->toArray(),
            'caller_namespace'=>$this->caller_namespace,
            'server'=>$this->server
        ];
    }

    protected static function fromArray(array $args) : static {
        $params = TypeParamData::from($args['design_params']);
        $given_type = static::getTypeFromArray('given_type',$args);;
        $caller_namespace =  static::getNamespaceFromArray('caller_namespace',$args);
        $server = static::getServerFromArray('server',$args,false);
        return new static(given_type: $given_type,params: $params,caller_namespace: $caller_namespace,server: $server);
    }

    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $new_design = $work->editDesign();
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $new_design->toArray());
    }

    /**
     * @throws \Throwable
     */
    protected  function editDesign() : ElementType {

        static::checkIfGivenIsAdmin(given: $this->caller_namespace,target: $this->given_type->owner_namespace);
        $this->given_type->checkInUse();
        try {
            DB::beginTransaction();
            if ($this->params->type_name) {
                $this->given_type->setTypeName(name: $this->params->type_name);
            }


            if ($this->params->schedule_ref_uuid) {
                $this->given_type->type_time_bound_id = TimeBound::getThisSchedule($this->params->schedule_ref_uuid)->id;
            }

            if ($this->server) {
                $this->given_type->imported_from_server_id = $this->server->id;
            }


            if (isset($this->params->is_final_type)) {
                $this->given_type->is_final_type = $this->params->is_final_type;
            }

            if ($this->params->handle_ref_uuid) {
                $this->given_type->type_handle_element_id = Element::getThisElement(uuid: $this->params->handle_ref_uuid)->id;
            }

            if ($this->given_type->isDirty()) {
                $this->given_type->save();
            }


            if (isset($this->params->access)) {
                //find the access
                $access = ElementTypeServerLevel::where('server_access_type_id',$this->given_type->id)->first();
                if (!$access) {
                    $access = new ElementTypeServerLevel();
                    $access->server_access_type_id = $this->given_type->id;
                    $access->to_server_id = Server::getDefaultServer()->id;
                }
                $access->access_type = $this->params->access;
                $access->save();
            }



            DB::commit();
            return $this->given_type;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

}

