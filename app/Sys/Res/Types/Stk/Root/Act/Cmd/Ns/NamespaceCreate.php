<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\Server;
use App\Models\User;
use App\Models\UserNamespace;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\DB;

class NamespaceCreate extends Act\Cmd\Ns
{
    const UUID = '2eb062ae-f06e-4b01-8a9f-2059f2fbc40b';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_CREATE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class,
    ];

    const EVENT_CLASSES = [
        Evt\Server\NamespaceCreated::class
    ];


    public function getCreatedNamespace(): ?UserNamespace
    {
        return $this->action_data->data_namespace;
    }

    public function getGivenServer(): ?Server
    {   /** @uses ActionDatum::data_server() */
        return $this->action_data->data_server;
    }

    public function getGivenUser(): ?User
    {   /** @uses ActionDatum::data_user() */
        return $this->action_data->data_user;
    }

    public function getGeneratedSet(): ?ElementSet
    {   /** @uses ActionDatum::data_set() */
        return $this->action_data->data_set;
    }

    public function getGeneratedType(): ?ElementType
    {   /** @uses ActionDatum::data_type() */
        return $this->action_data->data_type;
    }

    public function getGeneratedPublicElement(): ?Element
    {   /** @uses ActionDatum::data_second_element() */
        return $this->action_data->data_second_element;
    }

    public function getGeneratedPrivateElement(): ?Element
    {   /** @uses ActionDatum::data_element() */
        return $this->action_data->data_element;
    }


    const array ACTIVE_DATA_KEYS = ['namespace_name','public_key','uuid','given_user_uuid','given_server_uuid', 'is_stub'];

    public function __construct(
        protected ?string      $namespace_name = null,
        protected ?string      $public_key = null,
        protected ?string      $uuid = null,
        protected ?string         $given_user_uuid = null,
        protected ?string         $given_server_uuid = null,
        protected bool         $is_stub = false,
        protected bool         $is_system = false,
        protected bool         $send_event = true,
        protected ?ActionDatum $action_data = null,
        protected ?int         $action_data_parent_id = null,
        protected ?int         $action_data_root_id = null,
        protected bool         $b_type_init = false
    )
    {
        if (!$this->given_server_uuid) {
            $this->given_server_uuid = Server::getDefaultServer()->ref_uuid;
        }
        parent::__construct(action_data: $this->action_data, b_type_init: $this->b_type_init,
            is_system: $this->is_system, send_event: $this->send_event,
            action_data_parent_id: $this->action_data_parent_id, action_data_root_id: $this->action_data_root_id);
    }



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->action_data->data_user_id = $this->given_user_uuid;

        if ($this->given_user_uuid) {
            $this->action_data->data_user_id = User::getUser(uuid: $this->given_server_uuid)->id;
        }

        if ($this->given_server_uuid) {
            $this->action_data->data_server_id = Server::getThisServer(uuid: $this->given_server_uuid)->id;
        }

        $this->action_data->save();
        return $this->action_data;
    }



    public function setChildActionResult(IThingAction $child): void {
        $b_save = false;

        if ($child instanceof Act\Cmd\Us\UserRegister && $child->getCreatedUser()) { //todo use the kids to set the generated fields
            $this->given_user_uuid = $child->getCreatedUser()->id;
            $b_save = $this->update_data_key('given_user_id',$this->given_user_uuid);
        }

        if ($b_save) {
            $this->action_data->save();
        }
    }

    public function getChildrenTree(): ?Tree
    {
        if ($this->is_stub) {return null;}

        $nodes = [];
        //todo set up the kids

        /*
          finish (db transaction for all, sync) holds flag to send events or not, and if system, holds ns object
                                                         its callback has open api for new user
                    create handle (p0)
                    create private element (p1)
                    create public element (p2)
                    create home set (p3)
                    create home element (p4)
                    create namespace (p5)
                        create type
                            make user

         */


        if (count($nodes)) {
            return new Tree(
                $nodes,
                ['rootId' => -1]
            );
        }
        return null;

    }


    /**
     * @throws \Exception
     */
    public function runAction(array $data = []): void
    {
        parent::runAction($data);
        try {
            DB::beginTransaction();
            $created_namespace = UserNamespace::createNamespace(
                namespace_name: $this->namespace_name, owning_user_id: $this->getGivenUser()?->id,
                server_id: $this->getGivenServer()?->id, ref: $this->uuid,
                type_id: $this->getGeneratedType()?->id,
                public_element_id: $this->getGeneratedPublicElement()?->id,
                private_element_id: $this->getGeneratedPrivateElement()?->id,
                home_set_id: $this->getGeneratedSet()?->id,
                public_key: $this->public_key, is_system: $this->is_system
            );


            $this->action_data->data_namespace_id = $created_namespace->id;
            $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
            if ($this->send_event) {
                $this->post_events_to_send = Evt\Server\NamespaceCreated::makeEventActions(source: $this, data: $this->action_data);
            }
            $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->setActionStatus(TypeOfThingStatus::THING_ERROR);
            throw $e;
        }

    }



    protected function getMyData() :array {
        return ['namespace'=>$this->getCreatedNamespace()];
    }

}

