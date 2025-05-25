<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;
use App\Models\Element;
use App\Models\Phase;

use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Illuminate\Support\Facades\DB;

/**
 * Edit element ownership or phase  without events or consideration for rules
 *
 */

class ElementEdit extends Act\Cmd\Ele
{
    const UUID = '384ef934-d5a3-45c0-99d6-80e80adfe631';
    const ACTION_NAME = TypeOfAction::CMD_ELEMENT_EDIT;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

    public function getEditedElement(): ?Element
    {
        /** @uses ActionDatum::data_element() */
        return $this->action_data->data_element;
    }

    public function getChangedPhase(): ?Phase
    {
        return $this->action_data->data_phase;
    }


    const array ACTIVE_DATA_KEYS = ['given_element_uuid','change_phase_uuid'];


    public function __construct(
        protected ?string               $given_element_uuid = null ,
        protected ?string              $change_phase_uuid = null,
        protected bool                $is_system = false,
        protected bool                $send_event = true,
        protected bool                $is_async = true,
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


    public function getActionPriority(): int
    {
        return 0;
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

        if (!$this->getEditedElement()) {
            throw new \InvalidArgumentException("Need element before can edit");
        }

        try {
            DB::beginTransaction();
            if ($this->getChangedPhase()) {
                $this->getEditedElement()->element_phase_id = $this->getChangedPhase();
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
        return ['element'=>$this->getEditedElement(),'changed_phase'=>$this->getChangedPhase()];
    }



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        if ($this->given_element_uuid) {
            $this->action_data->data_element_id = Element::getThisElement(uuid: $this->given_element_uuid)->id;
        }

        if ($this->change_phase_uuid) {
            $this->action_data->data_phase_id = Phase::getThisPhase(uuid: $this->change_phase_uuid)->id;
        }
        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }

}

