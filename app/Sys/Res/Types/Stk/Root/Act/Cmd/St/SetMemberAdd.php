<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\St;

use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;
use App\Models\Element;
use App\Models\ElementSet;

use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\DB;

class SetMemberAdd extends Act\Cmd\St
{
    const UUID = 'ebd1275e-ecc6-486e-89cb-69e14ae4a44c';
    const ACTION_NAME = TypeOfAction::CMD_SET_MEMBER_ADD;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\St::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\SetEnter::class,
        Evt\Set\ShapeEnter::class,
        Evt\Set\MapEnter::class,
        Evt\Set\TypeMapEnclosedStart::class,
        Evt\Set\TypeMapEnclosingStart::class,
        Evt\Set\TypeShapeEnclosedStart::class,
        Evt\Set\TypeShapeEnclosingStart::class,
    ];


    public function getSetUsed(): ?ElementSet
    {
        /** @uses ActionDatum::data_set() */
        return $this->action_data->data_set;
    }

    protected function changeSetUsed(ElementSet $set) : void {
        $this->action_data->data_set_id = $set->id;
        $this->given_set_uuid = $set->ref_uuid;
        $this->action_data->collection_data =$this->getInitialConstantData();
        $this->action_data->save();
    }

    /**
     * @return Element[]
     */
    public function getElementsAdded(): array
    {
        return $this->action_data->getCollectionOfType(class: Element::class,partition_flag: 2);
    }

    /**
     * @return Element[]
     */
    public function getElementsGiven(): array
    {
        return $this->action_data->getCollectionOfType(class: Element::class,partition_flag: 0);
    }

    /**
     * @param Element[] $elements
     */
    protected function addElementsGiven(array $elements): void
    {
        $uuids = [];
        foreach ($elements as $element) {
            $uuids[] = $element->ref_uuid;
        }
        $this->given_element_uuids = array_unique( array_merge($this->given_element_uuids,$uuids) );
        $this->saveCollectionKeys();
        $this->action_data->collection_data =$this->getInitialConstantData();
        $this->action_data->save();
        if ($this->auto_allow_given_elements) {
            $this->addElementsAllowed(elements: $elements);
        }
    }

    /**
     * @param Element[] $elements
     */
    protected function addElementsAllowed(array $elements): void
    {
        $uuids = [];
        foreach ($elements as $element) {
            $uuids[] = $element->ref_uuid;
        }
        $this->allowed_element_uuids = array_unique( array_merge($this->allowed_element_uuids,$uuids) );
        $this->saveCollectionKeys();
        $this->action_data->collection_data =$this->getInitialConstantData();
        $this->action_data->save();
    }

    /**
     * @return Element[]
     */
    public function getElementsAllowed(): array
    {
        return $this->action_data->getCollectionOfType(class: Element::class,partition_flag: 1);
    }

    /**
     * @var string[] $added_element_uuids
     */
    protected array $added_element_uuids = [];

    /**
     * @var string[] $allowed_element_uuids
     */
    protected array $allowed_element_uuids = [];

    const array ACTIVE_DATA_KEYS = ['given_set_uuid','is_sticky','auto_allow_given_elements'];

    const array ACTIVE_COLLECTION_KEYS = [
        'given_element_uuids'=>['class'=>Element::class,'partition'=>0] ,
        'added_element_uuids'=>['class'=>Element::class,'partition'=>2] ,
        'allowed_element_uuids'=>['class'=>Element::class,'partition'=>1] ,
    ];
    public function __construct(
        protected ?string       $given_set_uuid = null ,
        /**
         * @var string[] $given_element_uuids
         */
        protected array        $given_element_uuids = [],
        protected bool         $is_sticky = false,
        protected bool         $auto_allow_given_elements = false,
        protected bool         $is_system = false,
        protected bool         $send_event = true,
        protected ?bool                $is_async = null,
        protected ?ActionDatum $action_data = null,
        protected ?ActionDatum        $parent_action_data = null,
        protected ?UserNamespace      $owner_namespace = null,
        protected bool         $b_type_init = false,
        protected array          $tags = []
    )
    {
        if ($this->is_system) {
            $this->allowed_element_uuids = $this->given_element_uuids;
        }
        parent::__construct(action_data: $this->action_data, parent_action_data: $this->parent_action_data,owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event,is_async: $this->is_async,tags: $this->tags);
    }



    /**
     * @throws \Exception
     */
    protected function runActionInner(array $data = []): void
    {
        parent::runActionInner();

        if (!$this->getSetUsed()) {
            throw new \InvalidArgumentException("Need set before can add member");
        }

        foreach ($this->getElementsAllowed() as $ele) {
            $namespace_to_use = $ele->element_namespace;
            if (!$namespace_to_use) { $namespace_to_use = $this->getNamespaceInUse();} //maybe being built for a new namespace
            $this->checkIfAdmin($namespace_to_use);
        }
        try {
            DB::beginTransaction();
            foreach ($this->getElementsAllowed() as $element) {
                $this->getSetUsed()->addElement(ele: $element,is_sticky: $this->is_sticky);
                $this->added_element_uuids[] = $element->ref_uuid;
            }
            $this->saveCollectionKeys();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }




    protected function getMyData() :array {
        return ['set'=>$this->getSetUsed(),'elements_added'=>$this->getElementsAdded(),'elements_given'=>$this->getElementsGiven()];
    }



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        if ($this->given_set_uuid) {
            $this->action_data->data_set_id = ElementSet::getThisSet(uuid: $this->given_set_uuid)->id;
        }

        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }

    public function getChildrenTree(): ?Tree
    {

        if ($this->send_event) {
            $nodes = [];
            $events = Evt\Set\SetEnter::makeEventActions(source: $this, action_data: $this->action_data);
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

    /**
     * @throws \Exception
     */
    public function setChildActionResult(IThingAction $child): void {


        if ($child instanceof Evt\Set\SetEnter) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else if($child->isActionSuccess()) {
                if ($this->given_set_uuid === $child->getAskedAboutSet()?->ref_uuid) {
                    $this->addElementsAllowed(elements: $child->getAllowedElements());
                }
            }
        }

        if ($child instanceof Act\Cmd\Ty\ElementCreate) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else if($child->isActionSuccess()) {
                $this->addElementsGiven(elements: $child->getElementsCreated());
            }
        }

        if ($child instanceof Act\Cmd\Ele\SetCreate) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else if($child->isActionSuccess()) {
                $this->changeSetUsed(set: $child->getCreatedSet());
            }
        }

    }

}

