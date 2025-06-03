<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Models\ActionDatum;

use App\Models\ElementSet;
use App\Models\ElementSetChild;
use App\Models\UserNamespace;

use App\OpenApi\Set\SetResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\DB;

#[HexbatchTitle( title: "New set")]
#[HexbatchBlurb( blurb: "Creates a new set from a given element")]
#[HexbatchDescription( description: "Any element can become a set without loosing any of its element functionality.
## Sets are the bedrock of the library

 * A set can optionally have a parent set.  Parents cannot be changed later. Children can be parents.
 * A set can choose to turn off events fired when an element enters or leaves it.
 * The owner of any set is the owner of its element, but elements can have their ownership changed
 * Sets can set up action hooks in the element type of do things when its content changes, or filter what can enter

 \" ' > <
")]
class SetCreate extends Act\Cmd\St
{
    const UUID = '06c6d184-1230-4bd1-9ee4-80657a9e3620';
    const ACTION_NAME = TypeOfAction::CMD_SET_CREATE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\St::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\SetCreated::class,
        Evt\Set\SetChildCreated::class
    ];




    public function getCreatedSet(): ?ElementSet
    {
        /** @uses ActionDatum::data_second_set() */
        return $this->action_data->data_second_set;
    }




    const array ACTIVE_DATA_KEYS = ['given_element_uuid','given_parent_set_uuid','uuid','set_has_events'];


    public function __construct(
        protected ?string               $given_element_uuid = null ,
        protected ?string              $given_parent_set_uuid = null,
        protected ?string             $uuid = null,
        protected bool                $set_has_events = true,
        protected bool                $is_system = false,
        protected bool                $send_event = true,
        protected ?bool                $is_async = null,
        protected ?ActionDatum        $action_data = null,
        protected ?ActionDatum        $parent_action_data = null,
        protected ?UserNamespace      $owner_namespace = null,
        protected bool                $b_type_init = false,
        protected array          $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data, parent_action_data: $this->parent_action_data,owner_namespace: $this->owner_namespace ,
            b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event,is_async: $this->is_async,tags: $this->tags);
    }


    /**
     * @throws \Exception
     */
    protected function runActionInner(array $data = []): void
    {
        parent::runActionInner();

        if (!$this->getGivenElement()) {
            throw new \InvalidArgumentException("Need element");
        }
        $namespace_to_use = $this->getGivenElement()->element_namespace;
        if (!$namespace_to_use) { $namespace_to_use = $this->getNamespaceInUse();} //maybe being built for a new namespace
        $this->checkIfAdmin($namespace_to_use);

        try {
            DB::beginTransaction();
            $set = new ElementSet();
            if ($this->uuid) {
                $set->ref_uuid = $this->uuid;
            }

            $set->parent_set_element_id = $this->getGivenElement()->id;
            $set->has_events = $this->set_has_events;
            $set->is_system = $this->is_system;
            $set->save();
            $this->action_data->data_second_set_id = $set->id;
            $this->action_data->save();

            if ($this->send_event) {
                $this->post_events_to_send = Evt\Server\SetCreated::makeEventActions(source: $this, action_data: $this->action_data,set_context: $set);
            }

            if ($this->getGivenSet()) {
                $rel = new ElementSetChild();
                $rel->parent_set_id = $this->getGivenSet()->id;
                $rel->child_set_id = $set->id;
                $rel->save();
                if ($this->send_event) {
                    $this->post_events_to_send =
                        array_merge($this->post_events_to_send,
                            Evt\Set\SetChildCreated::class::makeEventActions(source: $this, action_data: $this->action_data,set_context: $set) );
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }


    }


    protected function getMyData() :array {
        return ['element'=>$this->getGivenElement(),'given_parent'=>$this->getGivenSet(),'set'=>$this->getCreatedSet()];
    }

    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret = [];
        if (isset($what['set'])) {
            $ret['set'] = new SetResponse(given_set:  $what['set']);
        }

        return $ret;
    }



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->setGivenElement($this->given_element_uuid)->setGivenSet($this->given_parent_set_uuid);

        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }


    /**
     * @throws \Exception
     */
    public function setChildActionResult(IThingAction $child): void {



        if ($child instanceof Act\Cmd\Ty\ElementCreate) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else if($child->isActionSuccess()) {
                if (count($child->getElementsCreated()  ) === 1) {
                    $this->setGivenElement($child->getElementsCreated()[0],true);
                }
            }
        }

    }

}

