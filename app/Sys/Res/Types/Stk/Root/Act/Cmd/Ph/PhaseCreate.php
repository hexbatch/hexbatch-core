<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ph;

use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;
use App\Models\ElementType;
use App\Models\Phase;

use App\Models\UserNamespace;
use App\OpenApi\Phase\PhaseResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use Illuminate\Support\Facades\DB;

class PhaseCreate extends Act\Cmd\Ph
{
    const UUID = '24d33a5b-ed63-48f4-b45d-f729734af6ef';
    const ACTION_NAME = TypeOfAction::CMD_PHASE_CREATE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ph::class,
        Act\SystemPrivilege::class
    ];

    const EVENT_CLASSES = [
        Evt\Type\PhaseAdded::class,
    ];



    public function getGivenEditPhase(): ?Phase
    {
        /** @uses ActionDatum::data_phase() */
        return $this->action_data->data_phase;
    }

    public function getCreatedPhase(): ?Phase
    {
        /** @uses ActionDatum::data_second_phase() */
        return $this->action_data->data_second_phase;
    }




    const array ACTIVE_DATA_KEYS = ['given_type_uuid','given_edit_phase_uuid','uuid','phase_name','is_default_phase'];


    public function __construct(
        protected ?string             $given_type_uuid = null ,
        protected ?string             $given_edit_phase_uuid = null,
        protected ?string             $phase_name = null,
        protected ?string             $uuid = null,
        protected bool                $is_default_phase = false,
        protected bool                $is_system = false,
        protected bool                $send_event = true,
        protected ?bool                $is_async = null,
        protected ?ActionDatum        $action_data = null,
        protected ?ActionDatum        $parent_action_data = null,
        protected ?UserNamespace      $owner_namespace = null,
        protected bool                $b_type_init = false,
        protected array          $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data, parent_action_data: $this->parent_action_data,owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event,is_async: $this->is_async,tags: $this->tags);
    }

    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);

        $this->setGivenSet($this->given_edit_phase_uuid)->setGivenType($this->given_type_uuid);

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

        if (!$this->getGivenType() || !$this->phase_name) {
            throw new \InvalidArgumentException("Need type and name");
        }
        $this->checkIfAdmin($this->getGivenType()->owner_namespace);

        try {
            DB::beginTransaction();
            $phase = new Phase();
            if ($this->uuid) {
                $phase->ref_uuid = $this->uuid;
            }

            $phase->phase_type_id = $this->getGivenType()->id;
            $phase->setPhaseName($this->phase_name);
            if ($this->getGivenEditPhase()) {
                $phase->edited_by_phase_id = $this->getGivenEditPhase()->id;
            }
            if ($this->is_system) {
                $phase->is_default_phase = $this->is_default_phase;
            }
            $phase->save();
            $this->action_data->data_second_phase_id = $phase->id;
            $this->action_data->save();
            $this->action_data->refresh();
            if ($this->send_event) {
                $this->post_events_to_send = Evt\Type\PhaseAdded::makeEventActions(
                    source: $this, action_data: $this->action_data,phase_context: $phase);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }



    protected function getMyData() :array {
        return ['phase'=>$this->getCreatedPhase(),'given_edit_phase'=>$this->getGivenEditPhase(),'given_type'=>$this->getGivenType()];
    }

    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret = [];
        if (isset($what['phase'])) {
            $ret['phase'] = new PhaseResponse(given_phase:  $what['phase']);
        }

        return $ret;
    }





}

