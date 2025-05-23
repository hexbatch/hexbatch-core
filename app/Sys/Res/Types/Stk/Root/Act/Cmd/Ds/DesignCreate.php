<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Attributes\TypeOfServerAccess;
use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;
use App\Models\ElementType;
use App\Models\ElementTypeServerLevel;
use App\Models\Server;

use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Illuminate\Support\Facades\DB;


class DesignCreate extends Act\Cmd\Ds
{
    const UUID = 'f635c4b8-5903-4688-802c-c0b28f376be0';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_CREATE;


    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];


    public function getCreatedType(): ?ElementType
    {
        /** @uses ActionDatum::data_type() */
        return $this->action_data->data_type;
    }

    public function getGivenServer(): ?Server
    {   /** @uses ActionDatum::data_server() */
        return $this->action_data->data_server;
    }

    public function getGivenNamespace(): ?UserNamespace
    {
        /** @uses ActionDatum::data_namespace() */
        return $this->action_data->data_namespace;
    }

    const array ACTIVE_DATA_KEYS = ['type_name','owner_namespace_uuid','uuid','given_server_uuid','is_final'];


    public function __construct(
        protected ?string             $type_name =null,
        protected ?string                $owner_namespace_uuid = null,
        protected ?string                $given_server_uuid = null,
        protected bool                $is_final = false,
        protected ?TypeOfServerAccess $access = null,
        protected ?string             $uuid = null,
        protected bool                $is_system = false,
        protected bool                $send_event = false,
        protected ?ActionDatum        $action_data = null,
        protected ?int                $action_data_parent_id = null,
        protected ?int                $action_data_root_id = null,
        protected bool                $b_type_init = false
    )
    {
        if (!$this->given_server_uuid) {
            $this->given_server_uuid = Server::getDefaultServer(b_throw_on_missing: false)?->ref_uuid;
        }
        parent::__construct(action_data: $this->action_data, b_type_init: $this->b_type_init,
            is_system: $this->is_system, send_event: $this->send_event,
            action_data_parent_id: $this->action_data_parent_id, action_data_root_id: $this->action_data_root_id);
    }


    public function getActionPriority(): int
    {
        return 100;
    }

    protected function restoreData(array $data = []) {
        parent::restoreData($data);
        if ($this->action_data) {
            $access_string = $this->action_data->collection_data->offsetGet('access');
            $this->access = TypeOfServerAccess::tryFromInput($access_string);
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

        $this->action_data->collection_data->offsetSet('access',$this->access?->value);
        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }


    /**
     * @throws \Exception
     */
    public function runAction(array $data = []): void
    {
        parent::runAction($data);

        try {
            DB::beginTransaction();
            $type = new ElementType();
            if ($this->uuid) {
                $type->ref_uuid = $this->uuid;
            }

            $type->type_name = $this->type_name;

            $type->owner_namespace_id = $this->getGivenNamespace()?->id;
            $type->imported_from_server_id = $this->getGivenServer()?->id;
            $type->is_system = $this->is_system;
            $type->is_final_type = $this->is_final;
            $type->save();

            if ($this->access && $this->given_server_uuid) {
                $access = new ElementTypeServerLevel();
                $access->server_access_type_id = $type->id;
                $access->to_server_id = $type->imported_from_server_id;
                $access->access_type = $this->access;
                $access->save();
            }

            $this->action_data->data_type_id = $type->id;
            $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
            $this->action_data->refresh();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->setActionStatus(TypeOfThingStatus::THING_ERROR);
            throw $e;
        }

    }



    protected function getMyData() :array {
        return ['type'=>$this->getCreatedType()];
    }

}

