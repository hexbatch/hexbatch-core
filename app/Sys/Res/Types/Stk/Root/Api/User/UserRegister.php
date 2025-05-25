<?php

namespace App\Sys\Res\Types\Stk\Root\Api\User;


use App\Helpers\Annotations\Documentation\HexbatchBlurb;
use App\Helpers\Annotations\Documentation\HexbatchDescription;
use App\Helpers\Annotations\Documentation\HexbatchTitle;
use App\Models\ActionDatum;
use App\Models\User;
use App\Models\UserNamespace;
use App\OpenApi\Users\MeResponse;
use App\OpenApi\Users\Registration\RegistrationParams;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\ICallResponse;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\DB;

#[HexbatchTitle( title: "Register")]
#[HexbatchBlurb( blurb: "Creates a new user and his default namespace")]
#[HexbatchDescription( description: "

  Makes a new user, his default namespace including a new type, which is used to build the home set, and public and private elements")]
class UserRegister extends Api\UserApi
{
    const UUID = '6608f89f-ec12-427e-a653-9edc8acc5d19';
    const TYPE_NAME = 'api_user_register';


    const PARENT_CLASSES = [
        Api\UserApi::class,
        Act\Cmd\Us\UserRegister::class,
    ];


    protected function setCreatedNamespace(UserNamespace $namespace) : void {
        $this->action_data->data_namespace_id = $namespace->id;
        $this->action_data->save();
    }

    public function getCreatedNamespace(): UserNamespace
    {
        /** @uses ActionDatum::data_namespace() */
        return $this->action_data->data_namespace;
    }


    protected function setCreatedUser(User $user) : void {
        $this->action_data->data_user_id = $user->id;
        $this->action_data->save();
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
        protected bool $is_async = false,
        ?RegistrationParams $params = null,
        protected int            $priority = 0,
        protected array          $tags = []
    )
    {
        if($params) {
            if (!$this->user_name) { $this->user_name = $params->getUsername();}
            if (!$this->user_password) { $this->user_password = $params->getPassword();}
            if (!$this->public_key) { $this->public_key = $params->getPublicKey();}
        }
        parent::__construct(action_data: $this->action_data,  b_type_init: $this->b_type_init,is_async: $this->is_async,priority: $this->priority,tags: $this->tags);
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
            if ($this->getCreatedUser() && $this->getCreatedNamespace()) {
                $this->getCreatedNamespace()->namespace_user_id = $this->getCreatedUser()->id;
                $this->getCreatedNamespace()->save();
                $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
            } else {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            $this->action_data->refresh();
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
            user_name: $this->user_name,user_password: $this->user_password,is_system: false,send_event: true, parent_action_data: $this->action_data);
        $nodes[] = ['id' => $register->getActionData()->id, 'parent' => -1, 'title' => $register->getType()->getName(),'action'=>$register];

        $namespace = new Act\Cmd\Ns\NamespaceCreate(
            namespace_name: $this->user_name,is_system: false,send_event: true, parent_action_data: $this->action_data);
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

        if ($child instanceof Act\Cmd\Us\UserRegister) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else {
                if ($this->isActionSuccess() && $child->getCreatedUser()) {
                    $this->setCreatedUser(user: $child->getCreatedUser());
                }

            }

        }

        if ($child instanceof Act\Cmd\Ns\NamespaceCreate) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            } else {
                if ($this->isActionSuccess() && $child->getCreatedNamespace()) {
                    $this->setCreatedNamespace(namespace: $child->getCreatedNamespace());
                }
            }
        }

    }


    public static function runHook(array $header, array $body): ICallResponse
    {
        return new MeResponse(user: $body['user']);
    }



}

