<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Server;

use App\Enums\Server\TypeOfServerStatus;
use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;
use App\Models\ElementType;

use App\Models\Server;
use App\Models\UserNamespace;

use App\Sys\Res\Types\Stk\Root\Act;

use Hexbatch\Things\Enums\TypeOfThingStatus;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * asking elsewhere for new credentials
 */
class ServerPromote extends Act\Cmd\Server
{
    const UUID = '3fc91919-845c-4a9a-8261-db6de25db4b4';
    const ACTION_NAME = TypeOfAction::CMD_SERVER_PROMOTE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Server::class,
        Act\SystemPrivilege::class
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ServerRegistered::class
    ];


    public function getGivenType(): ?ElementType
    {
        /** @uses ActionDatum::data_type() */
        return $this->action_data->data_type;
    }

    public function getCreatedServer(): Server
    {
        /** @uses ActionDatum::data_server() */
        return $this->action_data->data_server;
    }

    public function getGivenNamespace(): ?UserNamespace
    {
        /** @uses ActionDatum::data_namespace() */
        return $this->action_data->data_namespace;
    }





    const array ACTIVE_DATA_KEYS = ['given_type_uuid','given_namespace_uuid','uuid','server_name','server_domain',
        'server_url','access_token_expires_at','server_access_token'];


    public function __construct(
        protected ?string              $given_type_uuid = null,
        protected ?string              $given_namespace_uuid = null,
        protected ?string             $server_name = null,
        protected ?string             $server_domain = null,
        protected ?string             $server_url = null,

        protected TypeOfServerStatus  $server_status = TypeOfServerStatus::UNKNOWN_SERVER,
        protected ?string             $access_token_expires_at = null,
        protected ?string             $server_access_token = null,

        protected ?string             $uuid = null,

        protected bool                $is_system = false,
        protected bool                $send_event = true,
        protected ?bool                $is_async = null,
        protected ?ActionDatum        $action_data = null,
        protected ?ActionDatum        $parent_action_data = null,
        protected ?UserNamespace      $owner_namespace = null,
        protected bool                $b_type_init = false,
        protected int            $priority = 0,
        protected array          $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data, parent_action_data: $this->parent_action_data,owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event,is_async: $this->is_async,priority: $this->priority,tags: $this->tags);
    }



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        if ($this->given_type_uuid) {
            $this->action_data->data_type_id = ElementType::getElementType(uuid: $this->given_type_uuid)->id;
        }
        if ($this->given_namespace_uuid) {
            $this->action_data->data_namespace_id = UserNamespace::getThisNamespace(uuid: $this->given_namespace_uuid)->id;
        }

        $this->action_data->collection_data->offsetSet('server_status',$this->server_status->value);
        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }

    protected function restoreData(array $data = []) {
        parent::restoreData($data);
        if ($this->action_data) {
            if ($this->action_data->collection_data?->offsetExists('server_status')) {
                $status_string = $this->action_data->collection_data->offsetGet('server_status');
                $this->server_status = TypeOfServerStatus::tryFromInput($status_string);
            }
        }
    }

    public function getInitialConstantData(): ?array {
        $ret = parent::getInitialConstantData();
        $ret['server_status'] = $this->server_status?->value;
        return $ret;
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
            $server = new Server();

            if ($this->uuid) {
                $server->ref_uuid = $this->uuid;
            }

            $server->owning_namespace_id = $this->getGivenNamespace()?->id;
            $server->server_type_id = $this->getGivenType()?->id;
            $server->server_status = $this->server_status ;

            if ($this->access_token_expires_at) {
                $server->access_token_expires_at = Carbon::parse($this->access_token_expires_at)->timezone('UTC')->toDateTimeString() ;
            }

            $server->server_access_token = $this->server_access_token ;
            $server->server_name = $this->server_name ;
            $server->server_domain = $this->server_domain ;
            $server->server_url = $this->server_url ;
            $server->is_system = $this->is_system ;



            $server->save();
            $this->action_data->data_server_id = $server->id;
            $this->action_data->save();
            $this->action_data->refresh();
            if ($this->send_event) {
                $this->post_events_to_send = Evt\Elsewhere\ServerRegistered::makeEventActions(
                    source: $this, action_data: $this->action_data,elsewhere_context: $server);
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
        return ['server'=>$this->getCreatedServer(),'given_namespace'=>$this->getGivenNamespace(),'given_type'=>$this->getGivenType()];
    }

}

