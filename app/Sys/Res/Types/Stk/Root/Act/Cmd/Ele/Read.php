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
use App\Models\ElementValue;
use App\Models\UserNamespace;
use App\OpenApi\Elements\ElementResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\DB;

#[HexbatchTitle( title: "Read an element")]
#[HexbatchBlurb( blurb: "Reads an attribute from an element")]
#[HexbatchDescription( description:'

if the type is not public,
 Only api or elements from the members of the group can read the value.

 If no read events, value is from the set context of element values
')]
class Read extends Act\Cmd\Ele
{
    const UUID = '6280f4c3-f2de-49c1-8b4e-5f3e7aab008c';
    const ACTION_NAME = TypeOfAction::PRAGMA_READ;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Pragma::class,
        Act\Cmd\Ele::class,
    ];

    const EVENT_CLASSES = [
        Evt\Set\Reading::class
    ];

    const array ACTIVE_DATA_KEYS = ['given_set_uuid','given_element_uuid','given_attribute_uuid','check_permission'];

    public function __construct(
        protected ?string              $given_set_uuid =null, //reads need a set
        protected ?string              $given_element_uuid =null, //reads need an element
        protected ?string              $given_attribute_uuid =null, //reads need an attribute
        protected bool                $check_permission = true,
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
            throw new \InvalidArgumentException("Need given set before reading");
        }

        if (!$this->getGivenAttribute()) {
            throw new \InvalidArgumentException("Need given attribute before reading");
        }

        if (!$this->getGivenElement()) {
            throw new \InvalidArgumentException("Need given element before reading");
        }

        $member = ElementSetMember::getMember(set:$this->getGivenSet(),element: $this->getGivenElement() );


        if ($this->check_permission) {
            //must have flag set or be in  group maybe
            if (!$this->hasFlag(TypeOfFlag::CAN_READ)) {
                $att = $this->getGivenAttribute();
                switch ($att->server_access_type) {
                    case TypeOfServerAccess::IS_PUBLIC_DOMAIN:
                    case TypeOfServerAccess::IS_PUBLIC: {
                        break;
                    }
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



        try {

            DB::beginTransaction();

            $val = ElementValue::readContextValue(
                member: $member,
                att: $this->getGivenAttribute(),
                type: $this->getGivenElement()->element_parent_type);
            $this->setImportantValue($val,true);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }



    protected function getMyData() :array {
        return ['element'=>$this->getGivenElement(),'set'=>$this->getGivenSet(),'value'=>$this->getImportantValue()];
    }


    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret = [];
        if (isset($what['element'])) {
            $ret['element'] = new ElementResponse(given_element:  $what['element']);
        }

        if (isset($what['value'])) {
            $ret['value'] = $what['value'];
        }
        return $ret;
    }

    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->setGivenSet($this->given_set_uuid)
            ->setGivenElement($this->given_element_uuid)
            ->setGivenAttribute($this->given_attribute_uuid);

        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }



    public function getChildrenTree(): ?Tree
    {

        if ($this->send_event && $this->getGivenSet() && $this->getGivenAttribute() && $this->getGivenElement()) {

            $nodes = [];
            $events = Evt\Set\Reading::makeEventActions(
                source: $this, action_data: $this->action_data,
                attribute_context: $this->getGivenAttribute(),
                set_context: $this->getGivenSet(),
                element_context: $this->getGivenElement(),
                important_value: $this->getImportantValue()
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


        if ($child instanceof Evt\Set\Reading) {
            if ($child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else if($child->isActionFail()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else if($child->isActionSuccess()) {
                $this->setFlag(TypeOfFlag::CAN_READ,true);
            }
        }

    }

}

