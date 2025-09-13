<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Us;

use App\Actions\Fortify\CreateNewUser;
use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;
use App\Models\User;
use App\Models\UserNamespace;
use App\OpenApi\Results\Users\MeResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\DB;

class UserRegister extends Act\Cmd\Us
{
    const string UUID = '2cca7cb0-4bde-4b66-ac54-302fba98853e';
    const TypeOfAction ACTION_NAME = TypeOfAction::CMD_USER_REGISTER;

    const array ATTRIBUTE_CLASSES = [

    ];

    const array PARENT_CLASSES = [
        Act\Cmd\Us::class,
        Act\SystemPrivilege::class,
    ];

    const array EVENT_CLASSES = [
        Evt\Server\UserRegistrationStarting::class,
        Evt\Server\UserRegistrationProcessing::class
    ];



    public function getCreatedUser(): ?User
    {
        return /** @uses ActionDatum::data_user() */
            $this->action_data->data_user;
    }


    const array ACTIVE_DATA_KEYS = ['user_name','user_password','uuid'];



    public function __construct(
        protected ?string      $user_name =null,
        protected ?string      $user_password = null,
        protected ?string      $uuid = null,
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


    /**
     * @throws \Exception
     */
    protected function runActionInner(array $data = []): void
    {
        parent::runActionInner();

        try {
            DB::beginTransaction();
            $created_user = null;
            if (!$this->getCreatedUser()) {
                $created_user = (new CreateNewUser)->create([
                    "username" => $this->user_name,
                    "password" => $this->user_password,
                    "password_confirmation" => $this->user_password
                ]);
                $this->action_data->data_user_id = $created_user->id;
                $this->action_data->save();
            }

            $b_save_again = false;

            if ($this->uuid && $created_user) {
                $created_user->ref_uuid = $this->uuid;
                $b_save_again = true;
            }

            if ($this->is_system && $created_user) {
                $b_save_again = true;
                $created_user->is_system = $this->is_system;
            }

            if ($b_save_again) {
                $created_user->save();
            }
            $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
            $this->action_data->refresh();
            if ($this->send_event) {
                $this->post_events_to_send = Evt\Server\UserRegistrationProcessing::makeEventActions(source: $this, action_data: $this->action_data);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

    }




    protected function getMyData() :array {
        return ['user'=>$this->getCreatedUser()];
    }

    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret = [];
        if (isset($what['user'])) {
            $ret['user'] = new MeResponse(user:  $what['user']);
        }

        return $ret;
    }


    public function getChildrenTree(): ?Tree
    {

        if ($this->send_event && !$this->is_system) {
            $nodes = [];
            $events = Evt\Server\UserRegistrationStarting::makeEventActions(source: $this, action_data: $this->action_data);

            foreach ($events as $event) {
                $nodes[] = ['id' => $event->getActionData()->id, 'parent' => -1, 'title' => $event->getType()->getName(),'action'=>$event];
            }

            //last in tree is the
            if (count($nodes)) {
                return new Tree(
                    $nodes,
                    ['rootId' => -1]
                );
            }
        }

        return null;
    }

    /**
     * @throws \Exception
     */
    public function setChildActionResult(IThingAction $child): void {


        if ($child instanceof Evt\Server\UserRegistrationStarting) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
        }

    }

}

