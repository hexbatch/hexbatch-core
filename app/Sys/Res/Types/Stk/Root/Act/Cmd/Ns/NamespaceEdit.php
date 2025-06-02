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

use App\OpenApi\UserNamespaces\UserNamespaceResponse;
use App\Sys\Res\Types\Stk\Root\Act;
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

    public function getEditedNamespace(): ?UserNamespace
    {
        return $this->getGivenNamespace();
    }


    public function getGivenUser(): ?User
    {   /** @uses ActionDatum::data_user() */
        return $this->action_data->data_user;
    }




    public function getGivenPublicElement(): ?Element
    {   /** @uses ActionDatum::data_second_element() */
        return $this->action_data->data_second_element;
    }

    public function getGivenPrivateElement(): ?Element
    {
        return $this->getGivenElement();
    }

    public function setGivenPrivateElement(null|Element|string $el)
    {
        return $this->setGivenElement($el,true);
    }


    const array ACTIVE_DATA_KEYS = ['given_namespace_uuid','namespace_name','public_key','given_user_uuid',
        'given_server_uuid',
        'given_public_element_uuid','given_private_element_uuid','given_home_set_uuid','given_type_uuid'
    ];

    public function __construct(
        protected ?string       $given_namespace_uuid = null,
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
        protected ?bool                $is_async = null,
        protected ?ActionDatum $action_data = null,
        protected ?ActionDatum        $parent_action_data = null,
        protected ?UserNamespace      $owner_namespace = null,
        protected bool         $b_type_init = false,
        protected array          $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data, parent_action_data: $this->parent_action_data,owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event,is_async: $this->is_async,tags: $this->tags);
    }


    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);

        $this->setGivenNamespace( $this->given_namespace_uuid)->setGivenServer($this->given_server_uuid);


        if ($this->given_type_uuid) {
            $this->action_data->data_type_id = ElementType::getElementType(uuid: $this->given_type_uuid)->id;
        }


        $this->setGivenSet($this->given_home_set_uuid);
        $this->setGivenPrivateElement($this->given_private_element_uuid);


        if ($this->given_public_element_uuid) {
            $this->action_data->data_second_element_id = Element::getThisElement(uuid: $this->given_public_element_uuid)->id;
        }

        if ($this->given_user_uuid) {
            $this->action_data->data_user_id = User::getUser(uuid: $this->given_user_uuid)->id;
        }
        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }


    /**
     * @throws \Exception
     */
    protected function runActionInner(array $data = []): void
    {
        parent::runActionInner();
        $namespace = $this->getEditedNamespace();
        if (!$namespace) {
            throw new \InvalidArgumentException("Need namespace to edit");
        }

        $this->checkIfAdmin($this->getEditedNamespace());
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

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }




    protected function getMyData() :array {
        return ['namespace'=>$this->getEditedNamespace()];
    }

    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret = [];
        if (isset($what['namespace'])) {
            $ret['namespace'] = new UserNamespaceResponse(namespace:  $what['namespace']);
        }

        return $ret;
    }

}

