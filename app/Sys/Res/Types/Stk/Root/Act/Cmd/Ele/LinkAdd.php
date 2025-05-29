<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Enums\Sys\TypeOfFlag;
use App\Models\ActionDatum;
use App\Models\ElementLink;
use App\Models\UserNamespace;
use App\OpenApi\Params\Actioning\Element\LinkCreateParams;
use App\OpenApi\Results\Elements\ElementResponse;
use App\OpenApi\Results\Set\LinkResponse;
use App\OpenApi\Results\Set\SetResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\DB;


#[HexbatchTitle( title: "Add a link")]
#[HexbatchBlurb( blurb: "Can link a set with an element")]
#[HexbatchDescription( description: /** @lang markdown */
    '
# Linking sets

Creates groups of sets or organize and do batch actions.

given_set_uuid: the set being the target
given_element_uuid: the element being the anchor

Any set can be linked, if no event handler for the element,
then only permission check is that the calling namespace is in element admin group

The element and type owners will recieve a

   * [LinkCreating](../../../Evt/Server/LinkCreating.php)

If all report back ok, then the link is made.

Once the link is made, the element and type owners will get an event
   * [LinkCreated](../../../Evt/Server/LinkCreated.php)


')]
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
        Evt\Server\LinkCreated::class,
        Evt\Server\LinkCreating::class,
    ];

    const PRE_EVENT_CLASS = Evt\Server\LinkCreating::class;
    const POST_EVENT_CLASS = Evt\Server\LinkCreated::class;

    const bool IS_ADDING = true;

    public static function getPreEventClass() : Evt\ScopeSet|string  { return static::PRE_EVENT_CLASS; }
    public static function getPostEventClass() : Evt\ScopeSet|string  { return static::POST_EVENT_CLASS; }


    const array ACTIVE_DATA_KEYS = ['given_set_uuid','given_element_uuid','check_permission'];



    #[ApiParamMarker( param_class: LinkCreateParams::class)]
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
            $this->setGivenLink(what: $link,b_save: true);
            if ($this->send_event) {
                $this->post_events_to_send = static::getPostEventClass()::makeEventActions(
                    source: $this, action_data: $this->action_data,
                    type_context: $this->getGivenType(),
                    set_context: $this->getGivenSet(),
                    element_context: $this->getGivenElement(),
                    link_context: $link
                );
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }



    protected function getMyData() :array {
        return [
            'element'=>$this->getGivenElement(),
            'link'=>$this->getGivenLink(),
            'set'=>$this->getGivenSet()
        ];
    }

    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret = [];
        if (isset($what['element'])) {
            $ret['element'] = new ElementResponse(given_element:  $what['element']);
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
            $events = static::getPreEventClass()::makeEventActions(
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


        if ($child instanceof (static::getPreEventClass()) ) {
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

