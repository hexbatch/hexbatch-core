<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Enums\Sys\TypeOfAction;

use App\Models\ActionDatum;
use App\Models\Element;

use App\Models\ElementType;
use App\Models\Phase;
use App\Models\UserNamespace;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\DB;

/**
 * if no handler for element creation, then only the type owner members can create
 *
 * This can create one or many elements at once
 * it can access a list of ns from a child to create one element per ns. This can be any ns.
 *  if no list, then the caller will be the element owner
 *
 * Creation can be blocked by the following
 * @see Evt\Type\ElementOwnerChange,Evt\Type\ElementRecieved,Evt\Type\ElementRecievedBatch,Evt\Type\ElementOwnerChangeBatch
 *
 * it can access a list of sets from a child to create one per set (and put them in the set)
 *  if no set provided, it will put new element in the caller's home set.
 *  the set the element is going to will be provided as context info for any event handlers
 *
 * if more than one element created, the batch version of the handler is called instead
 *
 */

class ElementCreate extends Act\Cmd\Ele
{
    const UUID = 'c21c5d03-685f-467b-afce-3ec449197eda';
    const ACTION_NAME = TypeOfAction::CMD_ELEMENT_CREATE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

    const EVENT_CLASSES = [
        Evt\Type\ElementCreation::class,
        Evt\Type\ElementCreationBatch::class,
        Evt\Element\ElementRecieved::class,
        Evt\Element\ElementRecievedBatch::class
    ];

    /**
     * @return Element[]
     */
    public function getElementsCreated(): array
    {
        return $this->action_data->getCollectionOfType(Element::class);
    }

    public function getNamespaceUsed(): ?UserNamespace
    {
        return $this->action_data->data_namespace;
    }

    public function getPhaseUsed(): ?Phase
    {
        /** @uses ActionDatum::data_phase() */
        return $this->action_data->data_phase;
    }

    protected function setTemplateType(ElementType $type) : void {
        $this->action_data->data_type_id = $type->id;
        $this->given_type_uuid = $type->ref_uuid;
        $this->action_data->collection_data =$this->getInitialConstantData();
        $this->action_data->save();
    }

    public function getTemplateType(): ?ElementType
    {
        return $this->action_data->data_type;
    }

    public function setNumberToMake(int $number_allowed) : void {
        $this->number_to_create = min($number_allowed,$this->number_to_create);
        $this->action_data->collection_data =$this->getInitialConstantData();
        $this->action_data->save();
    }

    /**
     * @var string[] $created_element_uuids
     */
    protected array $created_element_uuids = [];

    const array ACTIVE_COLLECTION_KEYS = ['created_element_uuids'=>Element::class];

    const array ACTIVE_DATA_KEYS = ['given_type_uuid','given_namespace_uuid','given_phase_uuid',
        'number_to_create','preassinged_uuids','b_must_have_namespace'];


    public function __construct(
        protected ?string       $given_type_uuid = null,
        protected ?string       $given_namespace_uuid = null,
        protected ?string      $given_phase_uuid = null,
        protected int          $number_to_create = 0,
        protected array        $preassinged_uuids = [],
        protected bool         $b_must_have_namespace = true,
        protected bool         $is_system = false,
        protected bool         $send_event = true,
        protected ?bool                $is_async = null,
        protected ?ActionDatum $action_data = null,
        protected ?ActionDatum        $parent_action_data = null,
        protected ?UserNamespace      $owner_namespace = null,
        protected bool         $b_type_init = false,
        protected int            $priority = 0,
        protected array          $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data, parent_action_data: $this->parent_action_data,owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event,is_async: $this->is_async,priority: $this->priority,tags: $this->tags);

    }



    /*
     * type the design
     * array uuid fo parent
     *
     */
    /**
     * @throws \Exception
     */
    public function runAction(array $data = []): void
    {
        parent::runAction($data);
        if ($this->isActionComplete()) {
            return;
        }
        if ($this->b_must_have_namespace && !$this->getNamespaceUsed()) {
            throw new \InvalidArgumentException("Need namespace before can make element");
        }

        if (!$this->getTemplateType()) {
            throw new \InvalidArgumentException("Need template type before can make element");
        }

        if (!$this->getTemplateType()->isPublished() ) {
            throw new \InvalidArgumentException(sprintf("Template type %s needs to be published before making element",$this->getTemplateType()->getName() ));
        }

        if ($this->number_to_create <= 0) {return;}
        $post_actions = [];
        $post_events = [];
        try {
            $this->created_element_uuids = [];
            $uuid_index = 0;

            DB::beginTransaction();



            for ($set_index = 0; $set_index < $this->number_to_create; $set_index++) {
                $this->makeElement(loop_number: $uuid_index++);
            } //end non set creation


            if (count($this->created_element_uuids) > 1 ) {
                if ($this->send_event) {
                    $this->post_events_to_send =
                        Evt\Element\ElementRecievedBatch::makeEventActions(source: $this, action_data: $this->action_data);
                }
            } else {
                if ($this->send_event) {
                    $this->post_events_to_send =
                        Evt\Element\ElementRecieved::makeEventActions(source: $this, action_data: $this->action_data);
                }
            }



            $this->saveCollectionKeys();
            $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
            $this->wakeLinkedThings();
            $this->action_data->refresh();
            if ($this->send_event) {
                $this->post_events_to_send = array_merge($post_actions,$post_events);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->setActionStatus(TypeOfThingStatus::THING_ERROR);
            throw $e;
        }

    }

    private function makeElement(int $loop_number) : void
    {

        $phase_id = $this->getPhaseUsed()?->id;
        $namespace_owner_id = $this->getNamespaceUsed()->id;
        $type_id = $this->getTemplateType()->id;

        $ele = new Element();
        $ele->element_parent_type_id = $type_id;
        $ele->element_phase_id = $phase_id;

        $ele->element_namespace_id = $namespace_owner_id;
        if (count($this->preassinged_uuids)) {
            $ele->ref_uuid = $this->preassinged_uuids[$loop_number]??null;
        }
        $ele->is_system = $this->is_system;
        $ele->save();
        $ele->refresh();
        $this->created_element_uuids[] = $ele->ref_uuid;
    }



    protected function getMyData() :array {
        return [
            'created_elements'=>$this->getElementsCreated(),'template_type'=>$this->getTemplateType(),
            'namespace_used'=>$this->getNamespaceUsed(),'phase_used'=>$this->getPhaseUsed()];
    }



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        if ($this->given_type_uuid) {
            $this->action_data->data_type_id =ElementType::getElementType(uuid: $this->given_type_uuid)->id;
        }

        if ($this->given_namespace_uuid) {
            $this->action_data->data_namespace_id =UserNamespace::getThisNamespace(uuid: $this->given_namespace_uuid)->id;
        }


        if ($this->given_phase_uuid) {
            $this->action_data->data_phase_id = Phase::getThisPhase(uuid: $this->given_phase_uuid)->id;
        } else {
            $this->action_data->data_phase_id = Phase::getDefaultPhase()?->id;
        }

        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }

    public function getChildrenTree(): ?Tree
    {

        if ($this->send_event && !$this->is_system) {
            $nodes = [];
            if (count($this->getElementsCreated()) > 1) {
                $events = Evt\Type\ElementCreationBatch::makeEventActions(source: $this, action_data: $this->action_data,
                    type_context: $this->getTemplateType());
            } else {
                $events = Evt\Type\ElementCreation::makeEventActions(source: $this, action_data: $this->action_data,
                    type_context: $this->getTemplateType());
            }

            foreach ($events as $event) {
                $nodes[] = ['id' => $event->getActionData()->id, 'parent' => -1, 'title' => $event->getType()->getName(),'action'=>$event];
            }

            //last in tree is the
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


        if ($child instanceof Evt\Type\ElementCreationBatch || $child instanceof Evt\Type\ElementCreation) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }

            else if($child->isActionSuccess()) {
                if ($child->getAskedAboutType() === $this->getTemplateType()) {
                    if ($this->getTemplateType()->isParentOfThis($child->getParentType())) //todo change to or ancestor later when doing element_type_ancestors
                    if ($child instanceof Evt\Type\ElementCreationBatch) {
                        $number_allowed = $child->getNumberAllowed();
                        if (null !== $number_allowed) {
                            $this->setNumberToMake($number_allowed);
                        }
                    }
                }
            }
        }


        if ($child instanceof TypePublish) {
            if ($child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else if($child->isActionSuccess() && $child->getPublishingType()) {
                $this->setTemplateType(type: $child->getPublishingType());
            }
        }

    }

}

