<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;

use App\Enums\Sys\TypeOfFlag;
use App\Exceptions\HexbatchNothingDoneException;
use App\Exceptions\RefCodes;
use App\Models\ActionDatum;
use App\Models\Element;
use App\Models\UserNamespace;
use App\OpenApi\Elements\ElementCollectionResponse;
use App\OpenApi\Params\Element\ChangeElementOwnerParams;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\DB;

#[HexbatchTitle( title: "Change element ownership")]
#[HexbatchBlurb( blurb: "Give one or more elements to a new owner, they can be mixed types")]
#[HexbatchDescription( description: /** @lang markdown */
    '
# Create elements

  This changes the ownership of one or more elements, these can be from different types.

  If no event handler to give permission to change ownership is set the type or owner ,
   then only the element admin members can create.

   * given_element_uuids : an array of uuid of the elements
   * given_new_namespace_uuid : the new element owner

  Either the type owner or new owner can have event handlers to block ownership change. Only one needs to fail this.


  Change can be blocked by the following:


  By the recipients (or type owner) who get

   * [ElementOwnerChange](../../../Evt/Type/ElementOwnerChange.php)

Either all the elements are accepted, or none accepted.
Any event handler to cancel the operation.


  After element ownership change, the recipent and type owner gets a notice

  * [ElementRecieved](../../../Evt/Type/ElementRecieved.php)


if more than one element created, the batch version of the handler is called instead



')]
class ElementOwnerChange extends Act\Cmd\Ele
{
    const UUID = '829b1a2d-8ed9-4950-8883-570c3517cfeb';
    const ACTION_NAME = TypeOfAction::CMD_ELEMENT_CHANGE_OWNER;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

    const EVENT_CLASSES = [
        Evt\Type\ElementOwnerChange::class,
        Evt\Type\ElementRecieved::class,
    ];

    /** @return Element[] */
    public function getElementsToGive(): array
    {
        return $this->action_data->getCollectionOfType(Element::class);
    }



    const array ACTIVE_COLLECTION_KEYS = ['given_element_uuids'=>Element::class];

    const array ACTIVE_DATA_KEYS = ['given_element_uuids','given_new_namespace_uuid'];


    #[ApiParamMarker( param_class: ChangeElementOwnerParams::class)]
    public function __construct(
        protected array          $given_element_uuids = [],
        protected ?string        $given_new_namespace_uuid = null,

        protected bool           $is_system = false,
        protected bool           $send_event = true,
        protected ?bool          $is_async = null,
        protected ?ActionDatum   $action_data = null,
        protected ?ActionDatum   $parent_action_data = null,
        protected ?UserNamespace $owner_namespace = null,
        protected bool           $b_type_init = false,
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


        if (count($this->getElementsToGive())  <= 0) {
            throw new HexbatchNothingDoneException(__("msg.elements_mising_from_give_list"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ELEMENTS_NOT_LISTED_TO_GIVE);
        }


        if (!$this->hasFlag(TypeOfFlag::CAN_WRITE)) {
            foreach ($this->getElementsToGive() as $element)
            //element admin check only
            $this->checkIfAdmin($element->element_namespace);
        }


        try {

            DB::beginTransaction();

            foreach ($this->getElementsToGive() as $element) {
                $element->changeOwners(namespace: $this->getGivenNamespace());
            }


            if ($this->send_event) {
                $this->post_events_to_send =
                    Evt\Type\ElementRecieved::makeEventActions(
                        source: $this, action_data: $this->action_data,important_array: $this->getElementsToGive());
            }


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }



    protected function getMyData() :array {
        return [
            'given_elements'=>$this->getElementsToGive(),
            'namespace_used'=>$this->getGivenNamespace()
        ];
    }

    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret = [];
        if (isset($what['given_elements'])) {
            $ret['given_elements'] = new ElementCollectionResponse(given_elements:  $what['given_elements']);
        }

        return $ret;
    }



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);

        $this->setGivenNamespace( $this->given_new_namespace_uuid);

        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }

    public function getChildrenTree(): ?Tree
    {

        if ($this->send_event && !$this->is_system) {
            $nodes = [];
            $owner_events = Evt\Type\ElementOwnerChange::makeEventActions(source: $this, action_data: $this->action_data,
                important_array: $this->getElementsToGive());


            foreach ($owner_events as $event) {
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


        if ($child instanceof Evt\Type\ElementOwnerChange) {
            if ($child->isActionError() || $child->isActionFail()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            } else if ($child->isActionSuccess()) {
                $this->setFlag(TypeOfFlag::CAN_WRITE,true);
            }
        }


    }

}

