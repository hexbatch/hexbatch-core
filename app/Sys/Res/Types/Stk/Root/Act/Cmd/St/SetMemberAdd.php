<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\St;

use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;
use App\Models\Element;
use App\Models\ElementSet;
use App\Sys\Res\Atr\Stk\Act\Metrics\SetMemberAddMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Illuminate\Support\Facades\DB;

class SetMemberAdd extends Act\Cmd\St
{
    const UUID = 'ebd1275e-ecc6-486e-89cb-69e14ae4a44c';
    const ACTION_NAME = TypeOfAction::CMD_SET_MEMBER_ADD;

    const ATTRIBUTE_CLASSES = [
        SetMemberAddMetric::class
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


    public function getSetUsed(): ElementSet
    {
        /** @uses ActionDatum::data_set() */
        return $this->action_data->data_set;
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

    const array ACTIVE_DATA_KEYS = ['given_set_uuid','is_sticky'];

    const array ACTIVE_COLLECTION_KEYS = [
        'given_element_uuids'=>['class'=>Element::class,'partition'=>0] ,
        'added_element_uuids'=>['class'=>Element::class,'partition'=>2] ,
        'allowed_element_uuids'=>['class'=>Element::class,'partition'=>1] ,
    ];
    public function __construct(
        protected string       $given_set_uuid ,
        /**
         * @var string[] $given_element_uuids
         */
        protected array        $given_element_uuids,
        protected bool         $is_sticky = false,
        protected bool         $is_system = false,
        protected bool         $send_event = false,
        protected ?ActionDatum $action_data = null,
        protected ?int         $action_data_parent_id = null,
        protected ?int         $action_data_root_id = null,
        protected bool         $b_type_init = false
    )
    {
        if ($this->is_system) {
            $this->allowed_element_uuids = $this->given_element_uuids;
        }
        parent::__construct(action_data: $this->action_data, b_type_init: $this->b_type_init,
            is_system: $this->is_system, send_event: $this->send_event,
            action_data_parent_id: $this->action_data_parent_id, action_data_root_id: $this->action_data_root_id);
    }


    public function getActionPriority(): int
    {
        return 0;
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


        try {
            DB::beginTransaction();
            foreach ($this->getElementsAllowed() as $element) {
                $this->getSetUsed()->addElement(ele: $element,is_sticky: $this->is_sticky);
                $this->added_element_uuids[] = $element->ref_uuid;
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
        return ['set'=>$this->getSetUsed(),'elements_added'=>$this->getElementsAdded(),'elements_given'=>$this->getElementsGiven()];
    }



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->action_data->data_set_id = ElementSet::getThisSet(uuid: $this->given_set_uuid)->id;
        $this->action_data->save();
        return $this->action_data;
    }

    public function getChildrenTree(): ?Tree
    {

        if ($this->send_event && !$this->is_system) {
            $nodes = [];
            $events = Evt\Set\SetEnter::makeEventActions(source: $this, data: $this->action_data);
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

}

