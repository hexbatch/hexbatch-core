<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;


use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Attributes\TypeOfServerAccess;
use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;
use App\Models\ElementType;
use App\Models\ElementTypeServerLevel;
use App\Models\Server;

use App\Models\TimeBound;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use Illuminate\Support\Facades\DB;

#[HexbatchTitle( title: "Design create")]
#[HexbatchBlurb( blurb: "Types are created here")]
#[HexbatchDescription( description: "
## Types can be set with the following properties

* type_uuid : when editing an existing type
* type_name: has to be unique in the namespace
* time_uuid: types can have a schedule
* is_final: cannot be a parent
* is_public_domain: if true, anyone can include as parent or in type without asking
* access: sets access across different servers

")]

class DesignCreate extends Act\Cmd\Ds
{
    const UUID = 'f635c4b8-5903-4688-802c-c0b28f376be0';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_CREATE;


    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];






    const array ACTIVE_DATA_KEYS = ['type_name','owner_namespace_uuid','uuid','given_server_uuid','is_final',
        'given_type_uuid','time_uuid','is_public_domain'];


    public function __construct(
        protected ?string             $type_name =null,
        protected ?string                $given_type_uuid = null,
        protected ?string                $owner_namespace_uuid = null,
        protected ?string                $given_server_uuid = null,
        protected ?string                $time_uuid = null,
        protected ?bool                $is_final = null,
        protected ?bool                $is_public_domain = null,
        protected ?TypeOfServerAccess $access = null,
        protected ?string             $uuid = null,
        protected bool                $is_system = false,
        protected bool                $send_event = true,
        protected ?bool                $is_async = null,
        protected ?ActionDatum        $action_data = null,
        protected ?ActionDatum        $parent_action_data = null,
        protected ?UserNamespace      $owner_namespace = null,
        protected bool                $b_type_init = false,
        protected array          $tags = []
    )
    {
        if (!$this->given_server_uuid) {
            $this->given_server_uuid = Server::getDefaultServer(b_throw_on_missing: false)?->ref_uuid;
        }
        parent::__construct(action_data: $this->action_data, parent_action_data: $this->parent_action_data, owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init,
            is_system: $this->is_system,
            is_public_domain: $this->is_public_domain, send_event: $this->send_event, is_async: $this->is_async,  tags: $this->tags);
    }



    protected function restoreData(array $data = []) {
        parent::restoreData($data);
        if ($this->action_data) {
            if ($this->action_data->collection_data?->offsetExists('access')) {
                $access_string = $this->action_data->collection_data->offsetGet('access');
                $this->access = TypeOfServerAccess::tryFromInput($access_string);
            }
        }
    }

    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        if ($this->given_server_uuid) {
            $this->action_data->data_server_id = Server::getThisServer(uuid: $this->given_server_uuid)->id;
        }

        if ($this->owner_namespace_uuid) {
            $this->action_data->data_namespace_id = UserNamespace::getThisNamespace(uuid: $this->owner_namespace_uuid)->id;
        }

        if ($this->given_type_uuid) {
            $this->action_data->data_type_id = ElementType::getElementType(uuid: $this->given_type_uuid)->id;
        }

        $this->action_data->collection_data->offsetSet('access',$this->access?->value);
        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }

    public function getInitialConstantData(): ?array {
        $ret = parent::getInitialConstantData();
        $ret['access'] = $this->access?->value;
        return $ret;
    }


    /**
     * @throws \Exception
     */
    protected function runActionInner(array $data = []): void
    {
        parent::runActionInner();

        try {
            DB::beginTransaction();
            $type = $this->getDesignType();
            if (!$type) {
                $type = new ElementType();
            }

            if ($this->uuid) {
                $type->ref_uuid = $this->uuid;
            }

            if ($this->type_name) {
                if ($this->is_system) {
                    $type->type_name = $this->type_name;
                } else {
                    $type->setTypeName(name: $this->type_name,namespace: $this->getNamespaceInUse());
                }

            }

            if (!$type->owner_namespace_id) {
                $type->owner_namespace_id = $this->getGivenNamespace()?->id;
            }

            if ($given_server_id =  $this->getGivenServer()?->id) {
                $type->imported_from_server_id = $given_server_id;
            }

            $type->is_system = $this->is_system;

            if ($this->is_final !== null) {
                $type->is_final_type = $this->is_final;
            }

            if ($this->is_public_domain !== null) {
                $type->is_public_domain = $this->is_public_domain;
            }

            if ($this->time_uuid) {
                $type->type_time_bound_id = TimeBound::getThisSchedule(uuid: $this->time_uuid)->id;
            }

            $type->save();

            if ($this->access && $this->given_server_uuid) {
                $access = new ElementTypeServerLevel();
                $access->server_access_type_id = $type->id;
                $access->to_server_id = $type->imported_from_server_id;
                $access->access_type = $this->access;
                $access->save();
            }

            $this->action_data->data_type_id = $type->id;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }





    protected function getMyData() :array {
        return ['type'=>$this->getDesignType()];
    }

}

