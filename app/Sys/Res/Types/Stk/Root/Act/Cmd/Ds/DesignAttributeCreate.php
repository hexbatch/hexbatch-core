<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Attributes\TypeOfElementValuePolicy;
use App\Enums\Attributes\TypeOfServerAccess;
use App\Enums\Sys\TypeOfAction;
use App\Enums\Types\TypeOfApproval;
use App\Enums\Types\TypeOfLifecycle;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\ActionDatum;
use App\Models\Attribute;
use App\Models\LocationBound;
use App\Models\UserNamespace;
use App\OpenApi\Attributes\AttributeResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\DB;

#[HexbatchTitle( title: "Attribute create")]
#[HexbatchBlurb( blurb: "Attributes are made in design phase only, an optional parent is added after creation")]
#[HexbatchDescription( description: "
## Attributes can be set with the following properties

* uuid : when editing an existing attribute
* type_uuid: each attribute is defined as part of a type, but can be inheritied by attributes elsewhere
* design_uuid: visually represents the attribute
* parent_uuid: this is set to pending, and the parent is notified to approve. If setting new parent, that one is asked to approve if not public domain
* location_uuid: attributes can have a shape
* is_final: cannot be a parent
* is_abstract: not usable by itself, must have a child
* access sets access across different servers
* value_policy: determines if the attribute can have multiple values for the same or all elements that use it
* read_json_path: if this is used, when the attribute value is always filtered by this
* validate_json_path: if this is used, when the attribute value is validated before being set
* default_value : if set, this is the default value before writing
* attribute_name: has to be unique in the namespace
* unset_parent : when editing an existing attribute and want to remove any parent

the type owner of the optional attribute parent will get a notice before creation

* [ElementTypeTurningOn](../../../Evt/Server/DesignPending.php)

This can decide to accept the new design using the parent or not, if they deny, the attribute is still created,
 but without the parent being approved, and it will be impossible to publish until this is changed
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

    protected function setAttributeApproval(TypeOfApproval $approval) {
        $this->action_data->collection_data->offsetSet('attribute_approval',$approval);
        $this->action_data->save();
    }

    const array ACTIVE_DATA_KEYS = ['attribute_name','owner_type_uuid','parent_attribute_uuid',
        'design_attribute_uuid','location_uuid','is_final','is_abstract','uuid','given_design_uuid','unset_parent',
        'read_json_path','validate_json_path','default_value'];

    protected TypeOfApproval     $attribute_approval = TypeOfApproval::PENDING_DESIGN_APPROVAL;

    public function __construct(
        protected ?string                  $given_design_uuid = null, //for editing an existing design
        protected ?string                  $uuid = null, //for assigning a uuid to a new type
        protected bool                     $unset_parent = false,  //for editing
        protected ?string                  $attribute_name = null,
        protected ?string                  $owner_type_uuid = null,
        protected ?string                  $parent_attribute_uuid = null,
        protected ?string                  $design_attribute_uuid = null,
        protected ?string                  $location_uuid = null,
        protected ?bool                     $is_final = null,
        protected ?bool                     $is_abstract = null,
        protected ?string                     $read_json_path = null,
        protected ?string                     $validate_json_path = null,
        protected array                     $default_value = [],
        protected ?TypeOfServerAccess       $access = null,
        protected ?TypeOfElementValuePolicy $value_policy = null,

        protected ?bool                    $is_async = null,
        protected bool                     $is_system = false,
        protected bool                     $send_event = true,
        protected ?ActionDatum             $action_data = null,
        protected ?ActionDatum             $parent_action_data = null,
        protected ?UserNamespace           $owner_namespace = null,
        protected bool                     $b_type_init = false,
        protected array                    $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data, parent_action_data: $this->parent_action_data,owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event,is_async: $this->is_async,tags: $this->tags);
    }


    protected function restoreData(array $data = []) {
        parent::restoreData($data);
        if ($this->action_data) {
            if ($this->action_data->collection_data?->offsetExists('access')) {
                $access_string = $this->action_data->collection_data->offsetGet('access');
                if ($access_string) {
                    $this->access = TypeOfServerAccess::tryFromInput($access_string);
                } else {
                    $this->access = null;
                }
            }

            if ($this->action_data->collection_data?->offsetExists('attribute_approval')) {
                $access_string = $this->action_data->collection_data->offsetGet('attribute_approval');
                $this->attribute_approval = TypeOfApproval::tryFromInput($access_string);
            }

            if ($this->action_data->collection_data?->offsetExists('value_policy')) {
                $policy_string = $this->action_data->collection_data->offsetGet('value_policy');
                if ($policy_string) {
                    $this->value_policy = TypeOfElementValuePolicy::tryFromInput($policy_string);
                } else {
                    $this->value_policy = null;
                }
            }

        }
    }

    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);


        $this->setGivenAttribute($this->given_design_uuid)->setGivenType($this->owner_type_uuid);


        if ($this->parent_attribute_uuid) {
            $this->action_data->data_second_attribute_id = Attribute::getThisAttribute(uuid: $this->parent_attribute_uuid)->id;
        }

        if ($this->design_attribute_uuid) {
            $this->action_data->data_third_attribute = Attribute::getThisAttribute(uuid: $this->design_attribute_uuid)->id;
        }

        $this->action_data->collection_data->offsetSet('access',$this->access?->value);
        $this->action_data->collection_data->offsetSet('value_policy',$this->value_policy?->value);
        $this->action_data->collection_data->offsetSet('attribute_approval',$this->attribute_approval->value);
        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }

    public function getInitialConstantData(): ?array {
        $ret = parent::getInitialConstantData();
        $ret['access'] = $this->access?->value;
        $ret['value_policy'] = $this->value_policy?->value;
        return $ret;
    }


    /**
     * @throws \Exception
     */
    protected function runActionInner(array $data = []): void {
        parent::runActionInner();

        if (!$this->getAttribute() && !$this->getDesignType()) {
            throw new \InvalidArgumentException("Need owning type before can make attribute");
        }

        if ($this->getDesignType()->lifecycle === TypeOfLifecycle::PUBLISHED) {

            throw new HexbatchNotPossibleException(__('msg.design_cannot_add_attribute_to_published',['ref'=>$this->getDesignType()->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::TYPE_SCHEMA_ISSUE);
        }

        if (!$this->getAttribute() && !$this->attribute_name) {
            throw new HexbatchNotPossibleException(__('msg.attribute_schema_must_have_name'),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
        }

        if ($this->getParentAttribute()?->is_final_attribute) {
            throw new HexbatchNotPossibleException(__('msg.attribute_parent_is_final',['ref'=>$this->getParentAttribute()->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
        }

        $shape_id = null;
        if ($this->location_uuid) {
            $shape_id = LocationBound::getThisLocation(uuid: $this->location_uuid)->id;
        }

        $this->checkIfAdmin($this->getDesignType()->owner_namespace);

        try {
            DB::beginTransaction();
            $attr = $this->getAttribute();
            if (!$attr) {
                $attr = new Attribute();
                if ($this->uuid) {
                    $attr->ref_uuid = $this->uuid;
                }
            }



            if ($this->attribute_name) {
                $attr->setAttributeName($this->attribute_name);
            }

            if (!$this->getAttribute()) {
                $attr->owner_element_type_id = $this->getDesignType()->id ;
                if (!$this->getParentAttribute()) {
                    $attr->attribute_approval = TypeOfApproval::DESIGN_APPROVED;
                }
            }

            if ($parent = $this->getParentAttribute()) {
                if ($this->getAttribute()?->parent_attribute_id !== $parent->id) {
                    $attr->attribute_approval = TypeOfApproval::PENDING_DESIGN_APPROVAL;
                }
                $attr->parent_attribute_id = $parent->id ;
            }

            if ($this->unset_parent) {
                $attr->parent_attribute_id = null;
                $attr->attribute_approval = TypeOfApproval::DESIGN_APPROVED;
            }

            if( $this->getDesignAttribute()) {
                if (!($this->is_system || $this->getDesignAttribute()->isPublicDomain()) ) {
                    $this->checkIfMember($this->getDesignAttribute()->type_owner->owner_namespace);
                }
                $attr->design_attribute_id = $this->getDesignAttribute()->id ;
            }

            if ($this->access) {
                $attr->server_access_type = $this->access ;
            }

            if ($this->value_policy) {
                $attr->value_policy = $this->value_policy ;
            }

            if ($this->read_json_path) {
                $attr->read_json_path = $this->read_json_path ;
            }

            if ($this->validate_json_path) {
                $attr->validate_json_path = $this->validate_json_path ;
            }

            if (!empty($this->default_value)) {
                $attr->setDefaultValue($this->default_value);
            }


            //public domain parents can be automatically approved
            if ($this->parent_attribute_uuid) {
                $par_attr = $this->getParentAttribute();
                if ($this->is_system || $par_attr->attribute_parent->isPublicDomain() ||
                    $par_attr->type_owner->owner_namespace->isNamespaceAdmin($this->getNamespaceInUse())
                )
                {
                    $attr->attribute_approval =  TypeOfApproval::DESIGN_APPROVED;
                } else {
                    $attr->attribute_approval = $this->attribute_approval;
                }

            }

            if ($this->is_final !== null ) {
                $attr->is_final_attribute = $this->is_final ;
            }



            if ($this->is_abstract !== null ) {
                $attr->is_abstract = $this->is_abstract ;
            }



            if ($shape_id) {
                $attr->attribute_shape_id = $shape_id;
            }
            $attr->save();


            $this->action_data->data_attribute_id = $attr->id;
            DB::commit();
        }
        catch (\Exception $e) {
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


    public function getChildrenTree(): ?Tree
    {
        if (!$this->send_event) {return null;}
        $nodes = [];
        $events = [];
        if ($this->getAttribute() && !$this->unset_parent) {
            if ($this->parent_attribute_uuid !== $this->getAttribute()->attribute_parent->ref_uuid) {
                if ($this->parent_attribute_uuid && !$this->getParentAttribute()->isPublicDomain()) {
                    $events = Evt\Server\DesignPending::makeEventActions(source: $this, action_data: $this->action_data,
                        type_context: $this->getDesignType(),attribute_context: $this->getParentAttribute());
                }
            }
        } else {
            if ( $this->parent_attribute_uuid && !$this->getParentAttribute()->isPublicDomain()) {
                $events = Evt\Server\DesignPending::makeEventActions(source: $this, action_data: $this->action_data,
                    type_context: $this->getDesignType(),attribute_context: $this->getParentAttribute());

            }
        }

        foreach ($events as $event) {
            $nodes[] = ['id' => $event->getActionData()->id, 'parent' => -1, 'title' => $event->getType()->getName(), 'action' => $event];
        }

        if (count($nodes)) {
            return new Tree(
                $nodes,
                ['rootId' => -1]
            );
        }


        return null;
    }

    /**
     * @throws \Exception
     */
    public function setChildActionResult(IThingAction $child): void
    {


        if ($child instanceof Evt\Server\DesignPending) {

            if ($child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            } else {

                if ($this->owner_type_uuid === $child->getAskedAboutType()?->ref_uuid) {
                    if ($this->attribute_name === $child->getAskedAboutAttributeName()) {
                        if ($child->isActionSuccess()) {
                            $this->setAttributeApproval(approval: TypeOfApproval::DESIGN_APPROVED);
                        } elseif ($child->isActionFail()) {
                            $this->setAttributeApproval(approval: TypeOfApproval::DESIGN_DENIED);
                        }
                    }
                }

            }
        } //end if this is design pending
    }

}

