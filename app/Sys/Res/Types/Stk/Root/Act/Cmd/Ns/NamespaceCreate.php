<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Attributes\TypeOfServerAccess;
use App\Enums\Sys\TypeOfAction;
use App\Enums\Types\TypeOfApproval;
use App\Models\ActionDatum;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\Server;
use App\Models\User;
use App\Models\UserNamespace;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Namespace\BasePerNamespace;
use App\Sys\Res\Types\Stk\Root\Namespace\HomeSet;
use App\Sys\Res\Types\Stk\Root\Namespace\PrivateType;
use App\Sys\Res\Types\Stk\Root\Namespace\PublicType;
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

    const BASE_TYPE_POSTFIX = '_base';

    protected function getBaseTypePostfix() : string {
        return $this->base_postfix? : static::BASE_TYPE_POSTFIX;
    }

    const PUBLIC_TYPE_POSTFIX = '_public';

    protected function getPublicTypePostfix() : string {
        return $this->public_postfix? : static::PUBLIC_TYPE_POSTFIX;
    }

    const PRIVATE_TYPE_POSTFIX = '_private';

    protected function getPrivateTypePostfix() : string {
        return $this->private_postfix? : static::PRIVATE_TYPE_POSTFIX;
    }

    const HOME_TYPE_POSTFIX = '_home';

    protected function getHomeTypePostfix() : string {
        return $this->home_postfix? : static::HOME_TYPE_POSTFIX;
    }


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

    protected function changeHomeSet(ElementSet $set) : void {
        $this->action_data->data_set_id = $set->id;
        $this->action_data->save();
    }

    public function getBaseType(): ?ElementType
    {   /** @uses ActionDatum::data_type() */
        return $this->action_data->data_type;
    }

    protected function changeBaseType(ElementType $type) : void {
        $this->action_data->data_type_id = $type->id;
        $this->action_data->save();
    }

    public function getGeneratedPublicElement(): ?Element
    {   /** @uses ActionDatum::data_second_element() */
        return $this->action_data->data_second_element;
    }

    public function getGeneratedPrivateElement(): ?Element
    {   /** @uses ActionDatum::data_element() */
        return $this->action_data->data_element;
    }

    protected function changePublicElement(Element $el): void
    {
         $this->action_data->data_second_element_id = $el->id;
         $this->action_data->save();
    }

    protected function changePrivateElement(Element $el): void
    {
        $this->action_data->data_element_id = $el->id;
        $this->action_data->save();
    }


    const array ACTIVE_DATA_KEYS = ['namespace_name','public_key','uuid','given_user_uuid','given_server_uuid', 'is_stub',
        'base_postfix','public_postfix','private_postfix','home_postfix'];

    public function __construct(
        protected ?string        $namespace_name = null,
        protected ?string        $public_key = null,
        protected ?string        $uuid = null,
        protected ?string        $given_user_uuid = null,
        protected ?string        $given_server_uuid = null,
        protected ?string        $base_postfix = null,
        protected ?string        $public_postfix = null,
        protected ?string        $private_postfix = null,
        protected ?string        $home_postfix = null,
        protected bool           $is_stub = false,
        protected bool           $is_system = false,
        protected bool           $send_event = true,
        protected ?bool                $is_async = null,
        protected ?ActionDatum   $action_data = null,
        protected ?ActionDatum   $parent_action_data = null,
        protected ?UserNamespace $owner_namespace = null,
        protected bool           $b_type_init = false,
        protected int            $priority = 0,
        protected array          $tags = []
    )
    {
        if (!$this->given_server_uuid) {
            $this->given_server_uuid = Server::getDefaultServer(b_throw_on_missing: false)?->ref_uuid;
        }
        parent::__construct(action_data: $this->action_data, parent_action_data: $this->parent_action_data,owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event,is_async: $this->is_async,priority: $this->priority,tags: $this->tags);
    }



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        if ($this->given_user_uuid) {
            $this->action_data->data_user_id = User::getUser(uuid: $this->given_user_uuid)->id;
        }


        if ($this->given_server_uuid) {
            $this->action_data->data_server_id = Server::getThisServer(uuid: $this->given_server_uuid)->id;
        }

        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }



    public function setChildActionResult(IThingAction $child): void {

        if ($child instanceof Act\Cmd\Ty\TypePublish) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            } else if($child->isActionSuccess()) {
                if ($child->getPublishingType()) {
                    $this->changeBaseType(type: $child->getPublishingType());
                }
            }
        }

        if ($child instanceof Act\Cmd\Ty\ElementCreate) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            if($child->isActionSuccess() && count($child->getElementsCreated()  ) === 1) {
                if (in_array('make private element', $child->getActionTags())) {
                    $private_element = $child->getElementsCreated()[0];
                    $this->changePrivateElement(el: $private_element);
                }
                else if (in_array('make public element', $child->getActionTags())) {
                    $this->changePublicElement($child->getElementsCreated()[0]);
                }
            }
        }

        if ($child instanceof Act\Cmd\Ele\SetCreate) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else if($child->isActionSuccess()) {
                $this->changeHomeSet(set: $child->getCreatedSet());
            }
        }

        if ($child instanceof Act\Cmd\St\SetMemberAdd) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
        }


    }

    public function getChildrenTree(): ?Tree
    {
        if ($this->is_stub) {return null;}



        $base_parent_guid = $this->action_data->data_owner_namespace?
                        $this->action_data->data_owner_namespace->user_base_type?->ref_uuid:
                        BasePerNamespace::getClassUuid();

        //all namespace elements are put in the default phase


        $add_to_set = new Act\Cmd\St\SetMemberAdd(auto_allow_given_elements: true, parent_action_data: $this->getActionData(), priority: 1, tags: ['add to home set','priority-1']);
        $home_set = new Act\Cmd\Ele\SetCreate(parent_action_data: $add_to_set->getActionData(),tags: ['make home set']);
        $public_type_element = new Act\Cmd\Ty\ElementCreate(number_to_create: 1,b_must_have_namespace: false, parent_action_data: $add_to_set->getActionData(), tags: ['make public element']);

        $pubic_type_publish = new Act\Cmd\Ty\TypePublish(
            parent_action_data: $public_type_element->getActionData(), publishing_status: TypeOfApproval::PUBLISHING_APPROVED, tags: ['publish public element']
        );

        $public_type_parent = new Act\Cmd\Ds\DesignParentAdd(
            given_parent_uuids: [PublicType::getClassUuid()], approval: TypeOfApproval::DESIGN_APPROVED, parent_action_data: $pubic_type_publish->getActionData(),
            tags: ['public type parent']
        );


        $public_type = new Act\Cmd\Ds\DesignCreate(
            type_name: $this->namespace_name.$this->getPublicTypePostfix(),access: TypeOfServerAccess::IS_PUBLIC, parent_action_data: $public_type_parent->getActionData(),
            tags: ['create public type']
        );

        $private_type_element = new Act\Cmd\Ty\ElementCreate(number_to_create: 1,b_must_have_namespace: false, parent_action_data: $add_to_set->getActionData(), priority: 1,
            tags: ['make private element','priority-1']);

        $private_type_publish = new Act\Cmd\Ty\TypePublish(
            parent_action_data: $private_type_element->getActionData(), publishing_status: TypeOfApproval::PUBLISHING_APPROVED,
            tags: ['publish private type']
        );

        $private_type_parent = new Act\Cmd\Ds\DesignParentAdd(
            given_parent_uuids: [PrivateType::getClassUuid()], approval: TypeOfApproval::DESIGN_APPROVED, parent_action_data: $private_type_publish->getActionData(),
            tags: ['private type parent']
        );

        $private_type = new Act\Cmd\Ds\DesignCreate(
            type_name: $this->namespace_name.$this->getPrivateTypePostfix(),access: TypeOfServerAccess::IS_PRIVATE, parent_action_data: $private_type_parent->getActionData(),
            tags: ['create private type']
        );

        $base_type_publish = new Act\Cmd\Ty\TypePublish(
            parent_action_data: $private_type_parent->getActionData(), publishing_status: TypeOfApproval::PUBLISHING_APPROVED,
            tags: ['publish base type']
        );

        $base_type_parent = new Act\Cmd\Ds\DesignParentAdd(
            given_parent_uuids: [$base_parent_guid],approval: TypeOfApproval::DESIGN_APPROVED,parent_action_data: $base_type_publish->getActionData(),
            tags: ['base type parent']
        );

        $base_type = new Act\Cmd\Ds\DesignCreate(
            type_name: $this->namespace_name.$this->getBaseTypePostfix(), access: TypeOfServerAccess::IS_PROTECTED,
            parent_action_data: $base_type_parent->getActionData(),
            tags: ['create base type']
        );



        $home_type_element = new Act\Cmd\Ty\ElementCreate(number_to_create: 1,b_must_have_namespace: false, parent_action_data: $home_set->getActionData(),
            tags: ['make home element']);

        $home_type_publish = new Act\Cmd\Ty\TypePublish(
            parent_action_data: $home_type_element->getActionData(), publishing_status: TypeOfApproval::PUBLISHING_APPROVED, tags: ['publish home element']
        );

        $home_type_parent = new Act\Cmd\Ds\DesignParentAdd(
            given_parent_uuids: [HomeSet::getClassUuid()], approval: TypeOfApproval::DESIGN_APPROVED, parent_action_data: $home_type_publish->getActionData(),
            tags: ['home type parent']
        );

        $home_type = new Act\Cmd\Ds\DesignCreate(
            type_name: $this->namespace_name.$this->getHomeTypePostfix(),access: TypeOfServerAccess::IS_PRIVATE, parent_action_data: $home_type_parent->getActionData(),
            tags: ['create home type']
        );


        //
        /*
         * need type first, then equally the home set element, the private
         * store reference for each handler made here, to do the below: to restore the action, if needed, and alter it

           uses A  (the publishing object) :$base_type_publish
           uses B (the home set element creation object) //set owner on type via the callback :$home_type_element
           uses C (the private element creation object) //set owner on type via the callback :$private_type_element
           uses D (the public element creation object) //set owner on type via the callback :$public_type_element
           uses E (the home set object) : $home_set

           add elements to set : $add_to_set
               make set (E) : $home_set
                   home-set element (B) ::$home_type_element
                       publish :$home_type_publish
                           add parent :$home_type_parent
                               make type (home set type) :$home_type
                               uses A

               make private element (C) :$private_type_element
                   publish :$private_type_publish
                       add parent :$private_type_parent
                           make type (private type) :$private_type
                           publish (A) :$base_type_publish
                                add parent :$base_type_parent
                                    make type (namespace base type) :$base_type

               make public element (D) :$public_type_element
                   publish :$pubic_type_publish
                       add parent :$public_type_parent
                           make type (public type) :$public_type
                           uses A

        publish, create set, create element needs to wake sleeping things
         */

        if ($this->send_event) {
            $nodes = [];
            $nodes[] = ['id' => $base_type_publish->getActionData()->id. '-single-a', 'parent' => -1, 'title' => $base_type_publish->getType()->getName(),'action'=>$base_type_publish,'is_waiting'=>true,'extra_tags'=>'waiting for base type to be published'];
            $nodes[] = ['id' => $home_type_element->getActionData()->id. '-single', 'parent' => -1, 'title' => $home_type_element->getType()->getName(),'action'=>$home_type_element,'is_waiting'=>true];
            $nodes[] = ['id' => $private_type_element->getActionData()->id. '-single', 'parent' => -1, 'title' => $private_type_element->getType()->getName(),'action'=>$private_type_element,'is_waiting'=>true];
            $nodes[] = ['id' => $public_type_element->getActionData()->id. '-single', 'parent' => -1, 'title' => $public_type_element->getType()->getName(),'action'=>$public_type_element,'is_waiting'=>true];
            $nodes[] = ['id' => $home_set->getActionData()->id. '-single', 'parent' => -1, 'title' => $home_set->getType()->getName(),'action'=>$home_set,'is_waiting'=>true];
            $nodes[] = ['id' => $add_to_set->getActionData()->id , 'parent' => -1, 'title' => $add_to_set->getType()->getName(),'action'=>$add_to_set,'priority'=>1];


                $nodes[] = ['id' => $home_set->getActionData()->id, 'parent' => $add_to_set->getActionData()->id, 'title' => $home_set->getType()->getName(),'action'=>$home_set,'extra_tags'=>'ps'];
                    $nodes[] = ['id' => $home_type_element->getActionData()->id, 'parent' => $home_set->getActionData()->id, 'title' => $home_type_element->getType()->getName(),'action'=>$home_type_element,'extra_tags'=>'ps|el'];
                        $nodes[] = ['id' => $home_type_publish->getActionData()->id, 'parent' => $home_type_element->getActionData()->id, 'title' => $home_type_publish->getType()->getName(),'action'=>$home_type_publish,'extra_tags'=>'ps|el|pub'];
                            $nodes[] = ['id' => $home_type_parent->getActionData()->id, 'parent' => $home_type_publish->getActionData()->id, 'title' => $home_type_parent->getType()->getName(),'action'=>$home_type_parent,'extra_tags'=>'ps|el|pub|par'];
                                $nodes[] = ['id' => $home_type->getActionData()->id, 'parent' => $home_type_parent->getActionData()->id, 'title' => $home_type->getType()->getName(),'action'=>$home_type,'extra_tags'=>'ps|el|pub|par|ty'];
                                $nodes[] = ['id' => $base_type_publish->getActionData()->id. '-single-b', 'parent' => $home_type_parent->getActionData()->id, 'title' => $base_type_publish->getType()->getName(),'action'=>$base_type_publish,'is_waiting'=>true,'extra_tags'=>['waiting for base type to be published','ps|el|pub|par|base_pub'] ];

                $nodes[] = ['id' => $private_type_element->getActionData()->id, 'parent' => $add_to_set->getActionData()->id, 'title' => $private_type_element->getType()->getName(),'action'=>$private_type_element,'extra_tags'=>'pv'];
                    $nodes[] = ['id' => $private_type_publish->getActionData()->id, 'parent' => $private_type_element->getActionData()->id, 'title' => $private_type_publish->getType()->getName(),'action'=>$private_type_publish,'extra_tags'=>'pv|pub'];
                        $nodes[] = ['id' => $private_type_parent->getActionData()->id, 'parent' => $private_type_publish->getActionData()->id, 'title' => $private_type_parent->getType()->getName(),'action'=>$private_type_parent,'extra_tags'=>'pv|pub|par'];
                            $nodes[] = ['id' => $private_type->getActionData()->id, 'parent' => $private_type_parent->getActionData()->id, 'title' => $private_type->getType()->getName(),'action'=>$private_type,'extra_tags'=>'pv|pub|par|ty'];
                            $nodes[] = ['id' => $base_type_publish->getActionData()->id, 'parent' => $private_type_parent->getActionData()->id, 'title' => $base_type_publish->getType()->getName(),'action'=>$base_type_publish,'extra_tags'=>'pv|pub|par|base_pub'];
                                $nodes[] = ['id' => $base_type_parent->getActionData()->id, 'parent' => $base_type_publish->getActionData()->id, 'title' => $base_type_parent->getType()->getName(),'action'=>$base_type_parent,'extra_tags'=>'pv|pub|par|base_pub|par'];
                                    $nodes[] = ['id' => $base_type->getActionData()->id, 'parent' => $base_type_parent->getActionData()->id, 'title' => $base_type->getType()->getName(),'action'=>$base_type,'priority'=>1,'extra_tags'=>'pv|pub|par|base_pub|par|ty'];

                $nodes[] = ['id' => $public_type_element->getActionData()->id, 'parent' => $add_to_set->getActionData()->id, 'title' => $public_type_element->getType()->getName(),'action'=>$public_type_element,'extra_tags'=>'pu'];
                    $nodes[] = ['id' => $pubic_type_publish->getActionData()->id, 'parent' => $public_type_element->getActionData()->id, 'title' => $pubic_type_publish->getType()->getName(),'action'=>$pubic_type_publish,'extra_tags'=>'pu|pub'];
                        $nodes[] = ['id' => $public_type_parent->getActionData()->id, 'parent' => $pubic_type_publish->getActionData()->id, 'title' => $public_type_parent->getType()->getName(),'action'=>$public_type_parent,'extra_tags'=>'pu|pub|par'];
                            $nodes[] = ['id' => $public_type->getActionData()->id, 'parent' => $public_type_parent->getActionData()->id, 'title' => $public_type->getType()->getName(),'action'=>$public_type,'extra_tags'=>'pu|el|pub|par|ty'];
                            $nodes[] = ['id' => $base_type_publish->getActionData()->id. '-single-c', 'parent' => $public_type_parent->getActionData()->id, 'title' => $base_type_publish->getType()->getName(),'action'=>$base_type_publish,'is_waiting'=>true,'extra_tags'=>['waiting for base type to be published','pu|el|pub|par|base_pub']];


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
        if ($this->isActionComplete()) {
            return;
        }
        try {
            DB::beginTransaction();
            $created_namespace = UserNamespace::createNamespace(
                namespace_name: $this->namespace_name, owning_user_id: $this->getGivenUser()?->id,
                server_id: $this->getGivenServer()?->id, ref: $this->uuid,
                type_id: $this->getBaseType()?->id,
                public_element_id: $this->getGeneratedPublicElement()?->id,
                private_element_id: $this->getGeneratedPrivateElement()?->id,
                home_set_id: $this->getGeneratedSet()?->id,
                public_key: $this->public_key, is_system: $this->is_system
            );


            $this->action_data->data_namespace_id = $created_namespace->id;

            $this->getGeneratedPrivateElement()->element_namespace_id = $created_namespace->id;
            $this->getGeneratedPrivateElement()->save();

            $this->getGeneratedPublicElement()->element_namespace_id = $created_namespace->id;
            $this->getGeneratedPublicElement()->save();

            $this->getGeneratedSet()->defining_element->element_namespace_id = $created_namespace->id;
            $this->getGeneratedSet()->defining_element->save();

            if ($this->send_event) {
                $this->post_events_to_send = Evt\Server\NamespaceCreated::makeEventActions(source: $this, action_data: $this->action_data);
            }
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
        return ['namespace'=>$this->getCreatedNamespace()];
    }

}

