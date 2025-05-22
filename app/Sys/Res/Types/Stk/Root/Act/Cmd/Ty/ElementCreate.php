<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Enums\Sys\TypeOfAction;

use App\Models\ActionDatum;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\Phase;
use App\Models\UserNamespace;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
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

    public function getNamespaceUsed(): UserNamespace
    {
        return $this->action_data->data_namespace;
    }

    public function getPhaseUsed(): ?Phase
    {
        /** @uses ActionDatum::data_phase() */
        return $this->action_data->data_phase;
    }

    /**
     * @return ElementSet[]
     */
    public function getSetsUsed(): array
    {
        return $this->action_data->getCollectionOfType(class: ElementSet::class);
    }


    public function getTemplateType(): ElementType
    {
        return $this->action_data->data_type;
    }

    /**
     * @var string[] $created_element_uuids
     */
    protected array $created_element_uuids = [];

    const array ACTIVE_COLLECTION_KEYS = ['created_element_uuids'=>Element::class,'given_set_uuids'=>ElementSet::class];

    const array ACTIVE_DATA_KEYS = ['given_type_uuid','given_namespace_uuid','given_phase_uuid','given_set_uuid',
        'number_to_create','preassinged_uuids'];


    public function __construct(
        protected string       $given_type_uuid ,
        protected string       $given_namespace_uuid ,
        protected ?string      $given_phase_uuid ,
        protected array        $given_set_uuids ,
        protected int          $number_to_create ,
        protected array        $preassinged_uuids = [],
        protected bool         $is_system = false,
        protected bool         $send_event = false,
        protected ?ActionDatum $action_data = null,
        protected ?int         $action_data_parent_id = null,
        protected ?int         $action_data_root_id = null,
        protected bool         $b_type_init = false
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
        if ($this->number_to_create <= 0) {return;}
        $post_actions = [];
        $post_events = [];
        try {
            $this->created_element_uuids = [];
            $uuid_index = 0;

            DB::beginTransaction();

            if (count($this->getSetsUsed())) {
                foreach ($this->getSetsUsed() as $set) {
                    $created_ele_to_set = [];
                    $ele = null;
                    for ($set_index = 0; $set_index < $this->number_to_create; $set_index++) {
                        $ele = $this->makeElement(loop_number: $uuid_index++);
                        $created_ele_to_set[] = $ele->ref_uuid;
                    } //end creating elements for the set
                    $post_actions[] = new Act\Cmd\St\SetMemberAdd(given_set_uuid: $set->ref_uuid,given_element_uuids: $created_ele_to_set,
                        is_system: $this->is_system,send_event: $this->send_event);

                    if (count($created_ele_to_set) > 1 ) {
                        if ($this->send_event) {
                            $post_events = array_merge($post_events,
                                Evt\Element\ElementRecievedBatch::makeEventActions(source: $this,data: $this->action_data));
                        }
                    } else {
                        if ($this->send_event) {
                            $post_events = array_merge($post_events,
                                Evt\Element\ElementRecieved::makeEventActions(source: $this,data: $this->action_data,
                                    element_context: $ele));
                        }


                    }
                }
            } else {
                //no set given
                for ($set_index = 0; $set_index < $this->number_to_create; $set_index++) {
                    $ele = $this->makeElement(loop_number: $uuid_index++);
                    if ($this->send_event) {
                        $post_events = array_merge($post_events,
                            Evt\Element\ElementRecieved::makeEventActions(source: $this, data: $this->action_data,
                                element_context: $ele));
                    }

                } //end non set creation
            }


            $this->restoreCollectionKeys();
            $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
            $this->post_events_to_send = array_merge($post_actions,$post_events);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->setActionStatus(TypeOfThingStatus::THING_ERROR);
            throw $e;
        }

    }

    private function makeElement(int $loop_number) : Element {

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
        return $ele;
    }



    protected function getMyData() :array {
        return [
            'created_elements'=>$this->getElementsCreated(),'template_type'=>$this->getTemplateType(),
            'sets_used' => $this->getSetsUsed(),
            'namespace_used'=>$this->getNamespaceUsed(),'phase_used'=>$this->getPhaseUsed()];
    }



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->action_data->data_type_id =ElementType::getElementType(uuid: $this->given_type_uuid)->id;
        $this->action_data->data_namespace_id =UserNamespace::getThisNamespace(uuid: $this->given_namespace_uuid)->id;

        if ($this->given_phase_uuid) {
            $this->action_data->data_phase_id = Phase::getThisPhase(uuid: $this->given_phase_uuid)->id;
        } else {
            $this->action_data->data_phase_id = Phase::getDefaultPhase()?->id;
        }

        $this->action_data->save();
        return $this->action_data;
    }

    public function getChildrenTree(): ?Tree
    {

        if ($this->send_event && !$this->is_system) {
            $nodes = [];
            if (count($this->getElementsCreated()) > 1) {
                $events = Evt\Type\ElementCreationBatch::makeEventActions(source: $this, data: $this->action_data,
                    type_context: $this->getTemplateType());
            } else {
                $events = Evt\Type\ElementCreation::makeEventActions(source: $this, data: $this->action_data,
                    type_context: $this->getTemplateType(),element_context: $this->getElementsCreated()[0]);
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

}

