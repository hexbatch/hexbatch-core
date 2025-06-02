<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Enums\Types\TypeOfApproval;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\ActionDatum;
use App\Models\Attribute;
use App\Models\ElementType;
use App\Models\UserNamespace;
use App\OpenApi\Attributes\AttributeResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use Illuminate\Support\Facades\DB;


class DesignAttributePromote extends Act\Cmd\Ds
{
    const UUID = 'b5ee5ca7-0e73-404c-800f-365ec668501d';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_ATTRIBUTE_PROMOTE;


    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class,
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
        protected bool                $b_type_init = false,
        protected array          $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data, parent_action_data: $this->parent_action_data,owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init, is_system: $this->is_system,
            send_event: $this->send_event,is_async: $this->is_async,tags: $this->tags);
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
    protected function runActionInner(array $data = []): void
    {
        parent::runActionInner();

        if (!$this->getDesignType()) {
            throw new \InvalidArgumentException("Need owning type before can make attribute");
        }

        if (!$this->attribute_name) {
            throw new HexbatchNotPossibleException(__('msg.attribute_schema_must_have_name'),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
        }

        try {
            DB::beginTransaction();
            $attr = new Attribute();
            if ($this->uuid) {
                $attr->ref_uuid = $this->uuid;
            }

            $attr->setAttributeName($this->attribute_name);
            $attr->attribute_approval = $this->attribute_approval;
            $attr->owner_element_type_id = $this->getDesignType()->id ;
            $attr->parent_attribute_id = $this->getParentAttribute()?->id ;
            $attr->design_attribute_id = $this->getDesignAttribute()?->id ;
            $attr->is_system = $this->is_system ;
            $attr->is_final_attribute = $this->is_final ;
            $attr->is_abstract = $this->is_abstract ;

            $attr->save();

            $this->setGivenAttribute($attr,true);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }




    protected function getMyData() :array {
        return ['attribute'=>$this->getAttribute(),'parent'=>$this->getParentAttribute(),'design'=>$this->getDesignAttribute()];
    }

    public function getDataSnapshot(): array
    {
        $ret = [];
        $what =  $this->getMyData();
        if (isset($what['attribute'])) {
            $ret['attribute'] = new AttributeResponse(given_attribute: $what['attribute']);
        }
        return $ret;
    }

}

