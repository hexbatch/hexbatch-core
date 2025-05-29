<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Enums\Types\TypeOfApproval;
use App\Models\ActionDatum;
use App\Models\Attribute;
use App\Models\ElementType;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Illuminate\Support\Facades\DB;

#[HexbatchTitle( title: "Attribute create")]
#[HexbatchBlurb( blurb: "Attributes are made in design phase only, an optional parent is added after creation")]
#[HexbatchDescription( description: "
## Attributes can be set with the following properties

* type_uuid: each attribute is defined as part of a type, but can be inheritied by attributes elsewhere
* design_uuid: visually represents the attribute
* parent uuid: this is set to pending, and the parent is notified to approve
* shape: attributes can have a shape
* is_final_attribute: cannot be a parent
* is_abstract: not usable by itself, must have a child
* public_domain: if true, anyone can include as parent or in type without asking
* server_access_type sets access across different servers
* set_value_policy: determines if the attribute can have multiple values for the same or all elements that use it
* read_json_path: if this is used, when the attribute value is always filtered by this
* validate_json_path: if this is used, when the attribute value is validated before being set
* attribute_name: has to be unique in the namespace
")]

class DesignAttributeCreate extends Act\Cmd\Ds
{
    const UUID = '47661774-8acc-45fb-8c22-77663177e92c';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_ATTRIBUTE_CREATE;

    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\DesignPending::class
    ];

    const array ACTIVE_DATA_KEYS = ['attribute_name','owner_type_uuid','parent_attribute_uuid',
        'design_attribute_uuid','is_final','is_abstract',
        'uuid'];


    public function __construct(
        protected ?string                $attribute_name = null,
        protected ?string                $owner_type_uuid = null,
        protected ?string                $parent_attribute_uuid = null,
        protected ?string                $design_attribute_uuid = null,
        protected bool                $is_final = false,
        protected bool                $is_abstract = false,
        protected TypeOfApproval     $attribute_approval = TypeOfApproval::PENDING_DESIGN_APPROVAL,
        protected ?string             $uuid = null,
        protected bool                $is_system = false,
        protected bool                $send_event = true,
        protected ?bool                  $is_async = null,
        protected ?ActionDatum        $action_data = null,
        protected ?ActionDatum        $parent_action_data = null,
        protected ?UserNamespace      $owner_namespace = null,
        protected bool                $b_type_init = false,protected int            $priority = 0,
        protected array          $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data, parent_action_data: $this->parent_action_data,owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event,is_async: $this->is_async,priority: $this->priority,tags: $this->tags);
    }


    protected function restoreData(array $data = []) {
        parent::restoreData($data);
        if ($this->action_data) {
            if ($this->action_data->collection_data?->offsetExists('attribute_approval')) {
                $access_string = $this->action_data->collection_data->offsetGet('attribute_approval');
                $this->attribute_approval = TypeOfApproval::tryFromInput($access_string);
            }

        }
    }

    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        if ($this->owner_type_uuid) {
            $this->action_data->data_type_id = ElementType::getElementType(uuid: $this->owner_type_uuid)->id;
        }

        if ($this->parent_attribute_uuid) {
            $this->action_data->data_second_attribute_id = Attribute::getThisAttribute(uuid: $this->parent_attribute_uuid)->id;
        }

        if ($this->design_attribute_uuid) {
            $this->action_data->data_third_attribute = Attribute::getThisAttribute(uuid: $this->design_attribute_uuid)->id;
        }

        $this->action_data->collection_data->offsetSet('attribute_approval',$this->attribute_approval?->value);
        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }

    public function getInitialConstantData(): ?array {
        $ret = parent::getInitialConstantData();
        $ret['attribute_approval'] = $this->attribute_approval?->value;
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

        if (!$this->getOwnerType()) {
            throw new \InvalidArgumentException("Need owning type before can make attribute");
        }

        if (!$this->attribute_name) {
            throw new \InvalidArgumentException("Need attr name given before can make attribute");
        }
        try {
            DB::beginTransaction();
            $attr = new Attribute();
            if ($this->uuid) {
                $attr->ref_uuid = $this->uuid;
            }

            $attr->setAttributeName($this->attribute_name);
            $attr->attribute_approval = $this->attribute_approval;
            $attr->owner_element_type_id = $this->getOwnerType()->id ;
            $attr->parent_attribute_id = $this->getParentAttribute()?->id ;
            $attr->design_attribute_id = $this->getDesignAttribute()?->id ;
            $attr->is_system = $this->is_system ;
            $attr->is_final_attribute = $this->is_final ;
            $attr->is_abstract = $this->is_abstract ;
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
        return ['attribute'=>$this->getAttribute(),'parent'=>$this->getParentAttribute(),'design'=>$this->getDesignAttribute()];
    }

}

