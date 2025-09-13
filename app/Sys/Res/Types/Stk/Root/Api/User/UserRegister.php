<?php

namespace App\Sys\Res\Types\Stk\Root\Api\User;


use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Models\ActionDatum;
use App\Models\User;
use App\Models\UserNamespace;
use App\OpenApi\Params\Actioning\Registration\RegistrationParams;
use App\OpenApi\Results\Users\MeResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IHookCode;
use Hexbatch\Things\Interfaces\IThingAction;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as CodeOf;

#[HexbatchTitle( title: "Register")]
#[HexbatchBlurb( blurb: "Creates a new user and his default namespace")]
#[HexbatchDescription( description: "

  Makes a new user, his default namespace including a new type, which is used to build the home set, and public and private elements")]
class UserRegister extends Api\UserApi implements IHookCode
{
    const UUID = '6608f89f-ec12-427e-a653-9edc8acc5d19';
    const TYPE_NAME = 'api_user_register';


    const PARENT_CLASSES = [
        Api\UserApi::class,
        Act\Cmd\Us\UserRegister::class,
    ];

    const int HTTP_CODE_GOOD = CodeOf::HTTP_CREATED;


    protected function setCreatedNamespace(UserNamespace $namespace) : void {
        $this->setGivenNamespace($namespace,true);
    }

    public function getCreatedNamespace(): ?UserNamespace
    {
        return $this->getGivenNamespace();
    }


    protected function setCreatedUser(User $user) : void {
        $this->action_data->data_user_id = $user->id;
        $this->action_data->save();
    }

    public function getCreatedUser(): ?User
    {
        /** @uses ActionDatum::data_user() */
        return $this->action_data->data_user;
    }



    protected function getMyData() :array {
        return ['user'=>$this->getCreatedUser(),'namespace'=>$this->getCreatedNamespace()];
    }

    public function getDataSnapshot(): array|IThingBaseResponse
    {
        $what =  $this->getMyData();
        return new MeResponse(user:  $what['user'],show_namespace: true,thing: $this->getMyThing());
    }

    public function __construct(
        protected ?ActionDatum   $action_data = null,
        protected bool $b_type_init = false,
        protected ?bool $is_async = null,
        protected ?RegistrationParams $params = null,
        protected array          $tags = []
    )
    {
        parent::__construct(action_data: $this->action_data,  b_type_init: $this->b_type_init,
            is_async: $this->is_async,tags: $this->tags);
    }

    protected function restoreParams(array $param_array) {
        parent::restoreParams($param_array);
        if(!$this->params) {
            $this->params = new RegistrationParams();
            $this->params->fromCollection(new Collection($param_array));
        }
    }


    /**
     * @throws \Exception
     */
    protected function runActionInner(array $data = []): void
    {
        parent::runActionInner();
        try {
            DB::beginTransaction();
            if ($this->getCreatedUser() && $this->getCreatedNamespace()) {
                $this->getCreatedNamespace()->namespace_user_id = $this->getCreatedUser()->id;
                $this->getCreatedNamespace()->save();

                $this->getCreatedUser()->default_namespace_id = $this->getCreatedNamespace()->id;
                $this->getCreatedUser()->save();
                $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
            } else {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            $this->action_data->refresh();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }




    public function getChildrenTree(): ?Tree
    {
        $this->action_data->refresh();

        $nodes = [];
        $register = new Act\Cmd\Us\UserRegister(
            user_name: $this->params->getUsername(), user_password: $this->params->getPassword(),
            is_system: false, send_event: true,
            parent_action_data: $this->action_data,tags: ['register user']);
        $nodes[] = ['id' => $register->getActionData()->id, 'parent' => -1, 'title' => $register->getType()->getName(),'action'=>$register];

        $namespace = new Act\Cmd\Ns\NamespaceCreate(
            namespace_name: $this->params->getUsername(),
            public_key: $this->params->getPublicKey(),
            is_system: false,send_event: true, parent_action_data: $this->action_data,tags: ['create namespace']);
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


    /**
     * @throws \Exception
     */
    public function setChildActionResult(IThingAction $child): void {

        if ($child instanceof Act\Cmd\Us\UserRegister) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else {
                if ($child->getCreatedUser()) {
                    $this->setCreatedUser(user: $child->getCreatedUser());
                }

            }

        }

        if ($child instanceof Act\Cmd\Ns\NamespaceCreate) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            } else {
                if ($child->getCreatedNamespace()) {
                    $this->setCreatedNamespace(namespace: $child->getCreatedNamespace());
                }
            }
        }

    }





}

