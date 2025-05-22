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
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Illuminate\Support\Facades\DB;


class NamespaceEdit extends Act\Cmd\Ns
{
    const UUID = '8db598cc-a4b5-43db-966b-d015e1316bb8';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_EDIT;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class
    ];

    public function getEditedNamespace(): UserNamespace
    {
        /** @uses ActionDatum::data_namespace() */
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

    public function getGivenSet(): ?ElementSet
    {   /** @uses ActionDatum::data_set() */
        return $this->action_data->data_set;
    }

    public function getGivenType(): ?ElementType
    {   /** @uses ActionDatum::data_type() */
        return $this->action_data->data_type;
    }

    public function getGivenPublicElement(): ?Element
    {   /** @uses ActionDatum::data_second_element() */
        return $this->action_data->data_second_element;
    }

    public function getGivenPrivateElement(): ?Element
    {   /** @uses ActionDatum::data_element() */
        return $this->action_data->data_element;
    }


    const array ACTIVE_DATA_KEYS = ['given_namespace_uuid','namespace_name','public_key','given_user_uuid',
        'given_server_uuid',
        'given_public_element_uuid','given_private_element_uuid','given_home_set_uuid','given_type_uuid'
    ];

    public function __construct(
        protected string       $given_namespace_uuid ,
        protected ?string      $namespace_name = null,
        protected ?string      $public_key = null,
        protected ?string         $given_user_uuid = null,
        protected ?string         $given_server_uuid = null,
        protected ?string         $given_type_uuid = null,
        protected ?string         $given_public_element_uuid = null,
        protected ?string         $given_private_element_uuid = null,
        protected ?string         $given_home_set_uuid = null,
        protected bool         $is_system = false,
        protected bool         $send_event = true,
        protected ?ActionDatum $action_data = null,
        protected ?int         $action_data_parent_id = null,
        protected ?int         $action_data_root_id = null,
        protected bool         $b_type_init = false
    )
    {

        parent::__construct(action_data: $this->action_data, b_type_init: $this->b_type_init,
            is_system: $this->is_system, send_event: $this->send_event,
            action_data_parent_id: $this->action_data_parent_id, action_data_root_id: $this->action_data_root_id);
    }


    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->action_data->data_namespace_id = UserNamespace::getThisNamespace(uuid: $this->given_namespace_uuid)->id;

        if ($this->given_server_uuid) {
            $this->action_data->data_server_id = Server::getThisServer(uuid: $this->given_server_uuid)->id;
        }

        if ($this->given_server_uuid) {
            $this->action_data->data_server_id = Server::getThisServer(uuid: $this->given_server_uuid)->id;
        }

        if ($this->given_type_uuid) {
            $this->action_data->data_type_id = ElementType::getElementType(uuid: $this->given_type_uuid)->id;
        }

        if ($this->given_home_set_uuid) {
            $this->action_data->data_set_id = ElementSet::getThisSet(uuid: $this->given_home_set_uuid)->id;
        }

        if ($this->given_private_element_uuid) {
            $this->action_data->data_element_id = Element::getThisElement(uuid: $this->given_private_element_uuid)->id;
        }

        if ($this->given_public_element_uuid) {
            $this->action_data->data_second_element_id = Element::getThisElement(uuid: $this->given_public_element_uuid)->id;
        }

        if ($this->given_user_uuid) {
            $this->action_data->data_user_id = User::getUser(uuid: $this->given_user_uuid)->id;
        }
        $this->action_data->save();
        return $this->action_data;
    }


    /**
     * @throws \Exception
     */
    public function runAction(array $data = []): void
    {
        parent::runAction($data);
        $namespace = $this->getEditedNamespace();
        try {
            DB::beginTransaction();
            if ($this->is_system) {
                if ($this->namespace_name) {
                    $namespace->setNamespaceName(name: $this->namespace_name);
                }

                if ($this->getGivenUser()) {
                    $namespace->namespace_user_id = $this->getGivenUser()->id;
                }

                if ($this->given_server_uuid) {
                    $namespace->namespace_server_id = $this->getGivenServer()->id;
                }

                if ($this->given_type_uuid) {
                    $namespace->namespace_type_id = $this->getGivenType()->id;
                }

                if ($this->given_home_set_uuid) {
                    $namespace->namespace_home_set_id = $this->getGivenSet()->id;
                }

                if ($this->given_public_element_uuid) {
                    $namespace->public_element_id = $this->getGivenPublicElement()->id;
                }

                if ($this->given_private_element_uuid) {
                    $namespace->private_element_id = $this->getGivenPrivateElement()->id;
                }
            }

            if ($this->public_key) {
                $namespace->namespace_public_key = $this->public_key;
            }

            $namespace->save();

            $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->setActionStatus(TypeOfThingStatus::THING_ERROR);
            throw $e;
        }

    }



    protected function getMyData() :array {
        return ['namespace'=>$this->getEditedNamespace()];
    }

}

