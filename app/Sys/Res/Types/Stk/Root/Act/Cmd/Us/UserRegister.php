<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Us;

use App\Actions\Fortify\CreateNewUser;
use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;
use App\Models\User;
use App\Sys\Res\Atr\Stk\Act\Metrics;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserRegister extends Act\Cmd\Us
{
    const string UUID = '2cca7cb0-4bde-4b66-ac54-302fba98853e';
    const TypeOfAction ACTION_NAME = TypeOfAction::CMD_USER_REGISTER;

    const array ATTRIBUTE_CLASSES = [
        Metrics\UserRegisterMetric::class
    ];

    const array PARENT_CLASSES = [
        Act\Cmd\Us::class,
        Act\SystemPrivilege::class,
    ];

    const array EVENT_CLASSES = [
        Evt\Server\UserRegistrationStarting::class,
        Evt\Server\UserRegistrationProcessing::class
    ];


    protected ?User $created_user = null;

    public function getCreatedUser(): ?User
    {
        return $this->created_user;
    }


    const array ACTIVE_DATA_KEYS = ['user_name','user_password','uuid'];



    public function __construct(
        protected ?string      $user_name =null,
        protected ?string      $user_password = null,
        protected ?string      $uuid = null,
        protected bool         $is_system = false,
        protected bool         $send_event = false,
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

    protected function restoreData(array $data = []) {
        parent::restoreData($data);
        if ($this->action_data) {
            if (!$this->created_user) {
                /** @uses ActionDatum::data_user() */
                $this->created_user = $this->action_data->data_user;
            }
        }
    }

    public function getActionPriority(): int
    {
        return 100;
    }


    /**
     * @throws ValidationException
     */
    public function runAction(array $data = []): void
    {
        parent::runAction($data);

        try {
            DB::beginTransaction();
            if (!$this->created_user) {
                $this->created_user = (new CreateNewUser)->create([
                    "username" => $this->user_name,
                    "password" => $this->user_password,
                    "password_confirmation" => $this->user_password
                ]);
                $this->action_data->data_user_id = $this->created_user->id;
                $this->action_data->save();
            }

            $b_save_again = false;

            if ($this->uuid) {
                $this->created_user->ref_uuid = $this->uuid;
                $b_save_again = true;
            }

            if ($this->is_system) {
                $b_save_again = true;
                $this->created_user->is_system = $this->is_system;
            }

            if ($b_save_again) {
                $this->created_user->save();
            }
            $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
            if ($this->send_event) {
                $this->post_events_to_send = Evt\Server\UserRegistrationProcessing::makeEventActions(source: $this, data: $this->action_data);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $this->setActionStatus(TypeOfThingStatus::THING_ERROR);
            throw $e;
        }


    }



    protected function getMyData() :array {
        return ['user'=>$this->created_user];
    }


    public function getChildrenTree(): ?Tree
    {

        if ($this->send_event && !$this->is_system) {
            $nodes = [];
            $events = Evt\Server\UserRegistrationStarting::makeEventActions(source: $this, data: $this->action_data);

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

    public function setChildActionResult(IThingAction $child): void {


        if ($child instanceof Evt\Server\UserRegistrationStarting) {
            if ($child->isActionFail()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
        }


    }


}

