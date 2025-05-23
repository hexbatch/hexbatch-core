<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;
use App\Models\Element;
use App\Models\Phase;

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
        protected bool                $send_event = false,
        protected ?ActionDatum        $action_data = null,
        protected ?int                $action_data_parent_id = null,
        protected ?int                $action_data_root_id = null,
        protected bool                $b_type_init = false
    )
    {

        parent::__construct(action_data: $this->action_data, b_type_init: $this->b_type_init,
            is_system: $this->is_system, send_event: $this->send_event,
            action_data_parent_id: $this->action_data_parent_id, action_data_root_id: $this->action_data_root_id);
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

