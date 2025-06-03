<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;

use App\Enums\Sys\TypeOfFlag;
use App\Models\ActionDatum;

use App\Models\ElementLink;
use App\Models\UserNamespace;
use App\OpenApi\Set\LinkerCollectionResponse;
use App\OpenApi\Set\LinkResponse;
use App\OpenApi\Set\SetResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\DB;

class LinkAdd extends Act\Cmd\Ele
{
    const UUID = '6eaef3f7-a458-459f-85aa-75d863677101';
    const ACTION_NAME = TypeOfAction::CMD_LINK_ADD;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\LinkCreated::class
    ];

    const EVENT_CLASS = Evt\Server\LinkCreated::class;

    const bool IS_ADDING = true;

    public static function getEventClass() : Evt\ScopeSet|string  { return static::EVENT_CLASS; }


    const array ACTIVE_DATA_KEYS = ['given_set_uuid','given_element_uuid','check_permission','link_uuid'];

    public function getCreatedLink() : ?ElementLink {
        if (!$this->link_uuid) {return null;}
        return ElementLink::buildLinks(uuid: $this->link_uuid)->first();
    }

    public function setCreatedLink(ElementLink $link) : static {
        $this->link_uuid = $link->ref_uuid;
        $this->action_data->collection_data->offsetSet('link_uuid',$link->ref_uuid);
        $this->action_data->save();
        return ElementLink::buildLinks(uuid: $this->link_uuid)->first();
    }
    protected ?string $link_uuid = null;
    public function __construct(
        protected ?string              $given_set_uuid =null,
        protected ?string              $given_element_uuid =null,


        protected bool                $check_permission = true,
        protected bool                $is_system = false,
        protected bool                $send_event = true,
        protected ?bool                $is_async = null,
        protected ?ActionDatum        $action_data = null,
        protected ?ActionDatum        $parent_action_data = null,
        protected ?UserNamespace      $owner_namespace = null,
        protected bool                $b_type_init = false,
        protected array               $tags = []
    )
    {
        parent::__construct(action_data: $this->action_data, parent_action_data: $this->parent_action_data,owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event,is_async: $this->is_async,tags: $this->tags);
    }


    /**
     * @throws \Exception
     */
    protected function runActionInner(array $data = []): void
    {
        parent::runActionInner();

        if (!$this->getGivenSet()) {
            throw new \InvalidArgumentException("Need given set before making link");
        }


        if (!$this->getGivenElement()) {
            throw new \InvalidArgumentException("Need given element before making link");
        }


        if ($this->check_permission) {
            //must have flag set or be in admin group
            if (!$this->hasFlag(TypeOfFlag::CAN_WRITE)) {
                //element admin check only
                $this->checkIfAdmin($this->getGivenElement()->element_namespace);
            }
        }


        try {

            DB::beginTransaction();
            if (static::IS_ADDING) {
               $link =  ElementLink::makeLink(el: $this->getGivenElement(),set: $this->getGivenSet());
            } else {
                $link = ElementLink::destroyLink(el: $this->getGivenElement(),set: $this->getGivenSet());
            }
            $this->setCreatedLink(link: $link);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }



    protected function getMyData() :array {
        return [
            'element'=>$this->getGivenElement(),
            'link'=>$this->getCreatedLink(),
            'set'=>$this->getGivenSet()
        ];
    }

    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret = [];
        if (isset($what['element'])) {
            $ret['element'] = new LinkerCollectionResponse(linker_element:  $what['element']);
        }

        if (isset($what['link'])) {
            $ret['link'] = new LinkResponse(linker:  $what['link']);
        }
        if (isset($what['set'])) {
            $ret['set'] = new SetResponse(given_set:  $what['set']);
        }

        return $ret;
    }



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->setGivenSet($this->given_set_uuid)
            ->setGivenElement($this->given_element_uuid);

        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }



    public function getChildrenTree(): ?Tree
    {

        if ($this->send_event && $this->getGivenSet() &&  $this->getGivenElement()) {

            $nodes = [];
            $events = static::getEventClass()::makeEventActions(
                source: $this, action_data: $this->action_data,
                type_context: $this->getGivenType(),
                set_context: $this->getGivenSet(),
                element_context: $this->getGivenElement()
            );


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


        if ($child instanceof (static::getEventClass()) ) {
            if ($child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else if($child->isActionFail()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else if($child->isActionSuccess()) {
                $this->setFlag(TypeOfFlag::CAN_WRITE,true);
            }
        }

    }

}

