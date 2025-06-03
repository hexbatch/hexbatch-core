<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

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
use App\OpenApi\Elements\ElementResponse;
use App\OpenApi\Set\SetResponse;
use App\OpenApi\Types\TypeResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\DB;


#[HexbatchTitle( title: "Turn off a type in an element")]
#[HexbatchBlurb( blurb: "Turns off all the attributes of a subtype in an element")]
#[HexbatchDescription( description:'
  When attributes are toggled off
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
        Evt\Set\ElementTypeOff::class,
    ];

    const bool MAKING_VISIBLE = false;

    const EVENT_CLASS = Evt\Set\ElementTypeOff::class;

    protected static function getEventClass() : Evt\ScopeSet|string  { return static::EVENT_CLASS; }


    const array ACTIVE_DATA_KEYS = ['given_set_uuid','given_element_uuid','given_type_uuid','check_permission'];

    public function __construct(
        protected ?string              $given_set_uuid =null,
        protected ?string              $given_element_uuid =null,
        protected ?string              $given_type_uuid =null,

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
            throw new \InvalidArgumentException("Need given set before turning off");
        }

        if (!$this->getGivenType()) {
            throw new \InvalidArgumentException("Need given type before turning off");
        }

        if (!$this->getGivenElement()) {
            throw new \InvalidArgumentException("Need given element before turning off");
        }

        if (!$this->getGivenElement()->element_parent_type->hasType(element_type: $this->getGivenType())) {
            throw new \InvalidArgumentException("Given element does not have the type");
        }

        $member = ElementSetMember::getMember(set:$this->getGivenSet(),element: $this->getGivenElement() );


        if ($this->check_permission) {
            //must have flag set or be in admin group
            if (!$this->hasFlag(TypeOfFlag::CAN_WRITE)) {
                //we do not check the permission for the element owner, just all the attributes in the element
                foreach ($this->getGivenType()->getAllAttributes() as $att)
                {
                    switch ($att->server_access_type) {
                        case TypeOfServerAccess::IS_PUBLIC_DOMAIN:
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
            }
        }




        try {

            DB::beginTransaction();
            ElementTypeSetVisibility::stateVisibility(
                visible_type_id: $this->getGivenType()->id,visible_set_member_id: $member->id,is_turned_on: static::MAKING_VISIBLE);

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
            'type'=>$this->getGivenType()
        ];
    }

    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret = [];
        if (isset($what['element'])) {
            $ret['element'] = new ElementResponse(given_element:  $what['element']);
        }

        if (isset($what['set'])) {
            $ret['set'] = new SetResponse(given_set:  $what['set']);
        }

        if (isset($what['type'])) {
            $ret['type'] = new TypeResponse(given_type:  $what['type']);
        }

        return $ret;
    }



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->setGivenSet($this->given_set_uuid)
            ->setGivenElement($this->given_element_uuid)
            ->setGivenType($this->given_type_uuid);

        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }



    public function getChildrenTree(): ?Tree
    {

        if ($this->send_event && $this->getGivenSet() && $this->getGivenType() && $this->getGivenElement()) {

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

