<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementSetChild;
use App\Sys\Res\Atr\Stk\Act\Metrics\SetCreateMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Illuminate\Support\Facades\DB;


class SetCreate extends Act\Cmd\St
{
    const UUID = '06c6d184-1230-4bd1-9ee4-80657a9e3620';
    const ACTION_NAME = TypeOfAction::CMD_SET_CREATE;

    const ATTRIBUTE_CLASSES = [
        SetCreateMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\St::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\SetCreated::class,
        Evt\Set\SetChildCreated::class
    ];


    public function getGivenElement(): Element
    {
        /** @uses ActionDatum::data_element() */
        return $this->action_data->data_element;
    }

    public function getGivenParent(): ?ElementSet
    {
        /** @uses ActionDatum::data_set() */
        return $this->action_data->data_set;
    }

    public function getCreatedSet(): ?ElementSet
    {
        /** @uses ActionDatum::data_second_set() */
        return $this->action_data->data_second_set;
    }




    const array ACTIVE_DATA_KEYS = ['given_element_uuid','given_parent_set_uuid','uuid','set_has_events'];


    public function __construct(
        protected string               $given_element_uuid ,
        protected ?string              $given_parent_set_uuid = null,
        protected ?string             $uuid = null,
        protected bool                $set_has_events = true,
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


        try {
            DB::beginTransaction();
            $set = new ElementSet();
            $set->ref_uuid = $this->uuid;
            $set->parent_set_element_id = $this->getGivenElement()?->id;
            $set->has_events = $this->set_has_events;
            $set->is_system = $this->is_system;
            $set->save();
            $this->action_data->data_second_set_id = $set->id;
            $this->action_data->save();

            if ($this->send_event) {
                $this->post_events_to_send = Evt\Server\SetCreated::makeEventActions(source: $this, data: $this->action_data,set_context: $set);
            }

            if ($this->getGivenParent()) {
                $rel = new ElementSetChild();
                $rel->parent_set_id = $this->getGivenParent()->id;
                $rel->child_set_id = $set->id;
                $rel->save();
                if ($this->send_event) {
                    $this->post_events_to_send =
                        array_merge($this->post_events_to_send,
                            Evt\Set\SetChildCreated::class::makeEventActions(source: $this, data: $this->action_data,set_context: $set) );
                }
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
        return ['element'=>$this->getGivenElement(),'given_parent'=>$this->getGivenParent(),'set'=>$this->getCreatedSet()];
    }



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->action_data->data_element_id = Element::getThisElement(uuid: $this->given_element_uuid)->id;
        if ($this->given_parent_set_uuid) {
            $this->action_data->data_set_id = ElementSet::getThisSet(uuid: $this->given_parent_set_uuid)->id;
        }
        $this->action_data->save();
        return $this->action_data;
    }

}

