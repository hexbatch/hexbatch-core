<?php

namespace App\Sys\Res\Types\Stk\Root\Api\User;


use App\Models\ActionDatum;
use App\Models\User;
use App\Models\UserNamespace;
use App\OpenApi\Users\MeResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\ICallResponse;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\DB;


class UserRegister extends Api\UserApi
{
    const UUID = '6608f89f-ec12-427e-a653-9edc8acc5d19';
    const TYPE_NAME = 'api_user_register';


    const PARENT_CLASSES = [
        Api\UserApi::class,
        Act\Cmd\Us\UserRegister::class,
    ];
    public function getCreatedNamespace(): UserNamespace
    {
        /** @uses ActionDatum::data_namespace() */
        return $this->action_data->data_namespace;
    }

    public function getCreatedUser(): User
    {
        /** @uses ActionDatum::data_user() */
        return $this->action_data->data_user;
    }


    const array ACTIVE_DATA_KEYS = ['user_name','user_password','public_key'];


    protected function getMyData() :array {
        return ['user'=>$this->getCreatedUser(),'namespace'=>$this->getCreatedNamespace()];
    }

    public function __construct(
        protected ?string $user_name =null,
        protected ?string $user_password = null,
        protected ?string $public_key = null,
        protected ?ActionDatum   $action_data = null,
        protected bool $b_type_init = false,
    )
    {
        parent::__construct(action_data: $this->action_data,  b_type_init: $this->b_type_init);
    }

    /**
     * @throws \Exception
     */
    public function runAction(array $data = []): void
    {
        try {
            DB::beginTransaction();
            if ($this->getCreatedUser() && $this->getCreatedNamespace()) {
                $this->getCreatedNamespace()->namespace_user_id = $this->getCreatedUser()->id;
                $this->getCreatedNamespace()->save();
                $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
            } else {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $this->setActionStatus(TypeOfThingStatus::THING_ERROR);
            throw $e;
        }

    }




    public function getChildrenTree(): ?Tree
    {
        $nodes = [];

        $register = new Act\Cmd\Us\UserRegister(
            user_name: $this->user_name,user_password: $this->user_password,is_system: false,send_event: true,
            action_data_parent_id: $this->action_data->id,action_data_root_id: $this->action_data->id);
        $nodes[] = ['id' => $register->getActionData()->id, 'parent' => -1, 'title' => $register->getType()->getName(),'action'=>$register];

        $namespace = new Act\Cmd\Ns\NamespaceCreate(
            namespace_name: $this->user_name,is_system: false,send_event: true,
            action_data_parent_id: $this->action_data->id,action_data_root_id: $this->action_data->id);
        $nodes[] = ['id' => $namespace->getActionData()->id, 'parent' => -1, 'title' => $namespace->getType()->getName(),'action'=>$namespace];



        //last in tree is the
        if (count($nodes)) {
            return new Tree(
                $nodes,
                ['rootId' => -1]
            );
        }
        return null;

    }


    public function setChildActionResult(IThingAction $child): void {


        if ($child instanceof Act\Cmd\Us\UserRegister && $child->getCreatedUser()) {
            if ($child->isActionFail() || !$child->isActionSuccess()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            } else {
                $this->action_data->data_user_id = $child->getCreatedUser()->id;
                $this->action_data->save();
            }

        }

        if ($child instanceof Act\Cmd\Ns\NamespaceCreate && $child->getCreatedNamespace()) {
            if ($child->isActionSuccess() && $child->getCreatedNamespace()) {
                $this->action_data->data_namespace_id = $child->getCreatedNamespace()->id;
                $this->action_data->save();
            } else {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }

        }

    }


    public static function runHook(array $header, array $body): ICallResponse
    {
        return new MeResponse(user: $body['user']);
    }



}

