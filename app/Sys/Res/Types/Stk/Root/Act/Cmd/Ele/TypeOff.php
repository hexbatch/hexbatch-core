<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Attributes\TypeOfServerAccess;
use App\Enums\Sys\TypeOfAction;
use App\Enums\Sys\TypeOfFlag;
use App\Models\ActionDatum;
use App\Models\ElementSetMember;
use App\Models\ElementTypeSetVisibility;
use App\Models\UserNamespace;
use App\OpenApi\Params\Actioning\Element\ElementSelectParams;
use App\OpenApi\Results\Elements\ElementActionResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\DB;


#[HexbatchTitle( title: "Turn off a type in an element")]
#[HexbatchBlurb( blurb: "Turns off all the attributes of a subtype in an element")]
#[HexbatchDescription( description: /** @lang markdown */
    '
  # When attributes are toggled off

  Attributes are organized by type, and subtypes of an element can be turned on and off for that element.
  This command turns off a type in an element

    given_set_uuid : optional to restrict this to one set
    given_element_uuid : optional to restrict to one element
    given_phase_uuid: optional to restrict to a phase
    given_type_uuid: required type to switch

  If no event handlers, then the element admin group AND
  a check for the caller being associated with each attribute in the type.

   * if the attribute is public domain no check
   * if attribute public or protected then must be a member of the type
   * if attribute private then must be an admin of the type

   But, event handling can be used. Each element owner and type owner is sent
   * [ElementTypeTurningOff](../../../Evt/Set/ElementTypeTurningOff.php)

   if all agree, then the type is turned off for that element

   and the element owner and type owners, and anyone else listening gets the following

   * [ElementTypeTurnedOff](../../../Evt/Set/ElementTypeTurnedOff.php)
')]

class TypeOff extends Act\Cmd\Ele
{
    const UUID = '2269dcbd-813d-431f-a8d4-c905012c927f';
    const ACTION_NAME = TypeOfAction::PRAGMA_TYPE_OFF;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class,
        Act\Pragma::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\ElementTypeTurningOff::class,
        Evt\Set\ElementTypeTurnedOff::class,
    ];

    const bool MAKING_VISIBLE = false;

    const PRE_EVENT_CLASS = Evt\Set\ElementTypeTurningOff::class;
    const POST_EVENT_CLASS = Evt\Set\ElementTypeTurnedOff::class;

    protected static function getPreEventClass() : Evt\ScopeSet|string  { return static::PRE_EVENT_CLASS; }
    protected static function getPostEventClass() : Evt\ScopeSet|string  { return static::POST_EVENT_CLASS; }


    const array ACTIVE_DATA_KEYS = ['given_set_uuid','given_element_uuid','given_type_uuid','given_phase_uuid','check_permission'];
    #[ApiParamMarker( param_class: ElementSelectParams::class)]
    public function __construct(
        protected ?string              $given_set_uuid =null,
        protected ?string              $given_element_uuid =null,
        protected ?string              $given_type_uuid =null,
        protected ?string              $given_phase_uuid =null,

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


        if (!$this->getGivenType()) {
            throw new \InvalidArgumentException("Need given type before turning off");
        }


        if ($this->getGivenElement() && !$this->getGivenElement()->element_parent_type->hasType(element_type: $this->getGivenType())) {
            throw new \InvalidArgumentException("Given element does not have the type");
        }

        $member = null;
        if ($this->getGivenSet() && $this->getGivenElement()) {
            $member = ElementSetMember::getMember(set:$this->getGivenSet(),element: $this->getGivenElement() );
        }



        if ($this->check_permission) {
            //must have flag set or be in admin group
            if (!$this->hasFlag(TypeOfFlag::CAN_WRITE)) {
                //we do not check the permission for the element owner, just all the attributes in the element
                foreach ($this->getGivenType()->getAllAttributes() as $att)
                {

                    switch ($att->server_access_type) {
                        case TypeOfServerAccess::IS_PUBLIC_DOMAIN: {
                            break;
                        }
                        case TypeOfServerAccess::IS_PUBLIC:
                        case TypeOfServerAccess::IS_PROTECTED: {
                            $this->checkIfMember($att->type_owner->owner_namespace);
                            break;
                        }
                        case TypeOfServerAccess::IS_PRIVATE: {
                            $this->checkIfAdmin($att->type_owner->owner_namespace);
                            break;
                        }
                    }
                }
                // do not check element level permissions, type can decide when it gets turned off regardless of who owns the element
            }
        }




        try {

            DB::beginTransaction();
            ElementTypeSetVisibility::stateVisibility(
                visible_type_id: $this->getGivenType()->id,visible_set_member_id: $member?->id,
                phase_id: $this->getGivenPhase()?->id ,
                is_turned_on: static::MAKING_VISIBLE);

            if ($this->send_event) {
                $this->post_events_to_send = static::getPostEventClass()::makeEventActions(
                    source: $this, action_data: $this->action_data,
                    type_context: $this->getGivenType(),
                    set_context: $this->getGivenSet(),
                    element_context: $this->getGivenElement()
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
            'set'=>$this->getGivenSet(),
            'phase'=>$this->getGivenPhase(),
            'type'=>$this->getGivenType()
        ];
    }

    public function getDataSnapshot(): array
    {
        $ret =  $this->getMyData();
        $what = [];
        $what['action'] = new ElementActionResponse(given_element: $ret['element'],given_set: $ret['set'],given_type: $ret['type'],given_phase: $ret['phase']);
        return $what;
    }



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->setGivenSet($this->given_set_uuid)
            ->setGivenElement($this->given_element_uuid)
            ->setGivenPhase($this->given_phase_uuid)
            ->setGivenType($this->given_type_uuid);

        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }



    public function getChildrenTree(): ?Tree
    {

        if ($this->send_event && $this->getGivenSet() && $this->getGivenType() && $this->getGivenElement()) {

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

