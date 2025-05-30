<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Enums\Sys\TypeOfAction;
use App\Enums\Types\TypeOfApproval;
use App\Enums\Types\TypeOfLifecycle;
use App\Models\ActionDatum;
use App\Models\Attribute;
use App\Models\ElementType;

use App\Models\ElementTypeParent;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt\Server\TypePublished;
use App\Sys\Res\Types\Stk\Root\Evt;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\DB;

/**
 * Publishes the type, any referenced parent types, parent attributes, live rules, live requirements
 * are given the event of @see TypePublished and all must agree
 */
class TypePublish extends Act\Cmd\Ty
{
    const UUID = 'af28da1b-b148-4cbf-a53f-ccaf641373ea';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_PUBLISH;


    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ty::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\TypePublished::class
    ];



    protected function getPublishingChildAttributeFromParentUiid(?string $uuid) :?Attribute
    {
        if (!$uuid) {return null;}
        foreach ($this->getGivenType()?->type_attributes as $attr) {
            if ($attr->attribute_parent?->ref_uuid === $uuid) {return $attr->attribute_parent;}
        }
        return null;
    }

    public function getPublishingType(): ?ElementType
    {
        return $this->action_data->data_type;
    }

    protected function setPublishingType(ElementType $type) : void {
        $this->action_data->data_type_id = $type->id;
        $this->given_type_uuid = $type->ref_uuid;
        $this->action_data->collection_data =$this->getInitialConstantData();
        $this->action_data->save();
    }


    const array ACTIVE_DATA_KEYS = ['given_type_uuid'];

    public function __construct(
        protected ?string              $given_type_uuid =null,
        protected bool                $is_system = false,
        protected bool                $send_event = true,
        protected ?bool                $is_async = null,
        protected ?ActionDatum        $action_data = null,
        protected ?ActionDatum        $parent_action_data = null,
        protected ?UserNamespace      $owner_namespace = null,
        protected bool                $b_type_init = false,
        protected TypeOfApproval $publishing_status = TypeOfApproval::APPROVAL_NOT_SET,
        protected int            $priority = 0,
        protected array          $tags = []
    )
    {
        parent::__construct(action_data: $this->action_data, parent_action_data: $this->parent_action_data,owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event,is_async: $this->is_async,priority: $this->priority,tags: $this->tags);
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
        $target = $this->getPublishingType();

        if (!$target) {
            throw new \InvalidArgumentException("Need type before can publish");
        }

        $this->checkIfAdmin($target->owner_namespace);
        $target->refresh();

        if ($target->lifecycle === TypeOfLifecycle::PUBLISHED) {
            throw new \RuntimeException("Type already published");
        }

        if (!$target->canBePublished() && ($this->publishing_status === TypeOfApproval::APPROVAL_NOT_SET)) {
            $parent_stuff_array = [];
            /** @var ElementTypeParent $parent */
            foreach ($target->type_parents as $parent) {
                $parent_stuff_array[] = sprintf(" %s -> %s ",$parent->parent_type->getName(),$parent->parent_type_approval->value);
            }
            throw new \RuntimeException(sprintf(" %s type cannot be published, its lifecycle is %s, its parents are %s. Parent count of %s",
                $target->getName(),$target->lifecycle->value,implode('|',$parent_stuff_array) ,
                count($target->type_parents)
            ));
        }
        try {

            DB::beginTransaction();
            if (!$this->send_event &&  ($this->publishing_status !== TypeOfApproval::APPROVAL_NOT_SET)) {
                //manually make this set for all parents , else they are set in the child answers
                foreach ($target->type_parents as $parent) {
                    /** @uses ElementTypeParent::parent_type() */
                    ElementTypeParent::updateParentStatus(parent: $parent->parent_type, child: $target, approval: $this->publishing_status);
                }

                foreach ($target->type_attributes as $attr) {
                    if ($attr->attribute_parent) {
                        $attr->attribute_approval = $this->publishing_status;
                        $attr->save();
                    }

                }
            } else {
                //public domain are automatically ok to publish
                foreach ($target->type_parents as $parent) {
                    if ($parent->is_public_domain) {
                        /** @uses ElementTypeParent::parent_type() */
                        ElementTypeParent::updateParentStatus(parent: $parent->parent_type, child: $target, approval: TypeOfApproval::PUBLISHING_APPROVED);
                    }
                }

                foreach ($target->type_attributes as $attr) {
                    if ($attr->attribute_parent) {
                        if ($attr->attribute_parent->is_public_domain) {
                            $attr->attribute_approval = TypeOfApproval::PUBLISHING_APPROVED;
                            $attr->save();
                        }
                    }
                }
            }




            //check to see if all parents have approved this design, if so then success, else fail
            /** @var ElementTypeParent[] $check_parents */
            $check_parents = ElementTypeParent::buildTypeParents(child_type_id: $target->id)->get();
            $my_status = TypeOfThingStatus::THING_SUCCESS;
            foreach ($check_parents as $checker) {
                if ($checker->parent_type_approval !== TypeOfApproval::PUBLISHING_APPROVED) {
                    $my_status = TypeOfThingStatus::THING_FAIL;
                    break;
                }
            }

            foreach ($target->type_attributes as $attr) {
                if ($attr->attribute_parent) {
                    if ($attr->attribute_approval !== TypeOfApproval::PUBLISHING_APPROVED) {
                        $my_status = TypeOfThingStatus::THING_FAIL;
                        break;
                    }
                }

            }

            if($my_status === TypeOfThingStatus::THING_SUCCESS) {
                $target->lifecycle = TypeOfLifecycle::PUBLISHED;
            }

            $target->save();
            $this->setActionStatus($my_status);
            $this->wakeLinkedThings();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->setActionStatus(TypeOfThingStatus::THING_ERROR);
            throw $e;
        }

    }



    protected function getMyData() :array {
        return ['type'=>$this->getPublishingType()];
    }



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        if ($this->given_type_uuid) {
            $this->action_data->data_type_id = ElementType::getElementType(uuid: $this->given_type_uuid)->id;
        }
        $this->action_data->collection_data->offsetSet('publishing_status',$this->publishing_status->value);
        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }

    protected function restoreData(array $data = []) {
        parent::restoreData($data);
        if ($this->action_data) {
            if ($this->action_data->collection_data?->offsetExists('publishing_status')) {
                $approval_string = $this->action_data->collection_data->offsetGet('publishing_status');
                $this->publishing_status = TypeOfApproval::tryFromInput($approval_string);
            }
        }
    }

    public function getInitialConstantData(): ?array {
        $ret = parent::getInitialConstantData();
        $ret['publishing_status'] = $this->publishing_status?->value;
        return $ret;
    }


    public function getChildrenTree(): ?Tree
    {

        if ($this->send_event && $this->getPublishingType()) {
            $events = [];
            $nodes = [];
            foreach ($this->getPublishingType()->type_parents as $parent) {
                if (!$parent->is_public_domain) {
                    $some_events = Evt\Server\TypePublished::makeEventActions(source: $this, action_data: $this->action_data,type_context: $parent);
                    $events =  array_merge($some_events,$events);
                }

            }

            foreach ($this->getPublishingType()->type_attributes as $attribute) {
                if($attribute->attribute_parent) {
                    if (!$attribute->attribute_parent->is_public_domain) {
                        $some_events = Evt\Server\TypePublished::makeEventActions(source: $this, action_data: $this->action_data,attribute_context: $attribute);
                        $events =  array_merge($some_events,$events);
                    }
                }
            }


            foreach ($events as $event) {
                $nodes[] = ['id' => $event->getActionData()->id, 'parent' => -1, 'title' => $event->getType()->getName(),'action'=>$event];
            }

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


        if ($child instanceof Evt\Server\TypePublished) {
            if ($child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else if($child->isActionSuccess()) {

                if ($this->given_type_uuid === $child->getAskedAboutType()?->ref_uuid) {
                    if(in_array($child->getParentType()->ref_uuid,$this->getPublishingType()->getParentUuids())) {
                        ElementTypeParent::updateParentStatus(parent: $child->getParentType(),
                            child: $child->getAskedAboutType(),approval: $child->getApprovalStatus());
                    }

                    if($attr = $this->getPublishingChildAttributeFromParentUiid(uuid: $child->getParentAttribute()?->ref_uuid))
                    {
                        $attr->attribute_approval = $child->getApprovalStatus();
                        $attr->save();
                    }
                }

            }
        }


        if ($child instanceof Act\Cmd\Ds\DesignParentAdd) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            } else if($child->isActionSuccess()) {
                if ($child->getDesignType()) {
                    $this->setPublishingType(type: $child->getDesignType());
                }
            }
        }

    }

}
