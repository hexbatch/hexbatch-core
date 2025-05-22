<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Enums\Types\TypeOfApproval;
use App\Models\ActionDatum;
use App\Models\Attribute;
use App\Models\ElementType;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignAttributePromoteMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Illuminate\Support\Facades\DB;


class DesignAttributePromote extends Act\Cmd\Ds
{
    const UUID = 'b5ee5ca7-0e73-404c-800f-365ec668501d';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_ATTRIBUTE_PROMOTE;


    const ATTRIBUTE_CLASSES = [
        DesignAttributePromoteMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class,
    ];

    public function getCreatedAttribute(): ?Attribute
    {
        /** @uses ActionDatum::data_attribute() */
        return $this->action_data->data_attribute;
    }

    public function getGivenOwnerType(): ?ElementType
    {   /** @uses ActionDatum::data_type() */
        return $this->action_data->data_type;
    }

    public function getGivenParentAttribute(): ?Attribute
    {
        /** @uses ActionDatum::data_second_attribute() */
        return $this->action_data->data_second_attribute;
    }

    public function getGivenDesignAttribute(): ?Attribute
    {
        /** @uses ActionDatum::data_third_attribute() */
        return $this->action_data->data_third_attribute;
    }

    const array ACTIVE_DATA_KEYS = ['attribute_name','owner_type_uuid','parent_attribute_uuid',
        'design_attribute_uuid','is_final','is_abstract','is_seen_in_child_elements',
        'uuid'];


    public function __construct(
        protected string                $attribute_name ,
        protected string                $owner_type_uuid,
        protected ?string                $parent_attribute_uuid = null,
        protected ?string                $design_attribute_uuid = null,
        protected bool                $is_final = false,
        protected bool                $is_abstract = false,
        protected bool                $is_seen_in_child_elements = false,
        protected TypeOfApproval     $attribute_approval = TypeOfApproval::PENDING_DESIGN_APPROVAL,
        protected ?string             $uuid = null,
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
        return 100;
    }

    protected function restoreData(array $data = []) {
        parent::restoreData($data);
        if ($this->action_data) {
            $access_string = $this->action_data->collection_data->offsetGet('attribute_approval');
            $this->attribute_approval = TypeOfApproval::tryFromInput($access_string);
        }
    }

    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->action_data->data_type_id = ElementType::getElementType(uuid: $this->owner_type_uuid)->id;
        if ($this->parent_attribute_uuid) {
            $this->action_data->data_second_attribute_id = Attribute::getThisAttribute(uuid: $this->parent_attribute_uuid)->id;
        }

        if ($this->design_attribute_uuid) {
            $this->action_data->data_third_attribute = Attribute::getThisAttribute(uuid: $this->design_attribute_uuid)->id;
        }

        $this->action_data->collection_data->offsetSet('attribute_approval',$this->attribute_approval?->value);
        $this->action_data->save();
        return $this->action_data;
    }


    /**
     * @throws \Exception
     */
    public function runAction(array $data = []): void
    {
        parent::runAction($data);

        try {
            DB::beginTransaction();
            $attr = new Attribute();
            $attr->ref_uuid = $this->uuid;
            $attr->attribute_name = $this->attribute_name;
            $attr->attribute_approval = $this->attribute_approval;
            $attr->owner_element_type_id = $this->getGivenOwnerType()->id ;
            $attr->parent_attribute_id = $this->getGivenParentAttribute()?->id ;
            $attr->design_attribute_id = $this->getGivenDesignAttribute()?->id ;
            $attr->is_system = $this->is_system ;
            $attr->is_final_attribute = $this->is_final ;
            $attr->is_abstract = $this->is_abstract ;
            $attr->is_seen_in_child_elements = $this->is_seen_in_child_elements ;
            $attr->save();


            $this->action_data->data_attribute_id = $attr->id;
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
        return ['attribute'=>$this->getCreatedAttribute(),'parent'=>$this->getGivenParentAttribute(),'design'=>$this->getGivenDesignAttribute()];
    }

}

