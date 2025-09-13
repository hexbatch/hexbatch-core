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
use App\OpenApi\Params\Actioning\Element\ElementSelectParams;
use App\OpenApi\Results\Elements\ElementCollectionResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\DB;

/*

 */
#[HexbatchTitle( title: "Destroy an element")]
#[HexbatchBlurb( blurb: "Can destroy one or more elements of different types")]
#[HexbatchDescription( description: /** @lang markdown */
    '
# Destory elements

One or more elements can be destroyed here, they can be of mixed types.

given_element_uuids: array of element uuids

If no event handler to handle deletion is set the type or owner ,
   then only the element admin members can destroy.

  Either the type owner or new owner can have event handlers to block destruction. Only one needs to fail this.


  Deletion can be blocked by the following:


  By the recipients (or type owner) who get

   * [ElementDestruction](../../../Evt/Type/ElementDestruction.php)

Once destroyed, there is a notice given to the user and the type owner
* [ElementDestroyed](../../../Evt/Type/ElementDestroyed.php)

 It is ok if an element is destroyed while things are working on it. They will fail, or they will finish without it.

')]
class ElementDestroy extends Act\Cmd\Ele
{
    const UUID = '557bbc2e-f589-4874-91f0-5d5e96fe115f';
    const ACTION_NAME = TypeOfAction::CMD_ELEMENT_DESTROY;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

    const EVENT_CLASSES = [
        Evt\Type\ElementDestruction::class,
        Evt\Type\ElementDestroyed::class
    ];

    /** @return Element[] */
    public function getElementsToDestroy(): array
    {
        return $this->action_data->getCollectionOfType(Element::class);
    }



    const array ACTIVE_COLLECTION_KEYS = ['given_element_uuids'=>Element::class];

    const array ACTIVE_DATA_KEYS = ['given_element_uuids','given_new_namespace_uuid'];


    #[ApiParamMarker( param_class: ElementSelectParams::class)]
    public function __construct(
        protected array          $given_element_uuids = [],

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


        if (count($this->getElementsToDestroy())  <= 0) {
            throw new HexbatchNothingDoneException(__("msg.elements_mising_from_destroy_list"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ELEMENTS_NOT_LISTED_TO_DESTROY);
        }


        if (!$this->hasFlag(TypeOfFlag::CAN_WRITE)) {
            foreach ($this->getElementsToDestroy() as $element)
                //element admin check only
                $this->checkIfAdmin($element->element_namespace);
        }


        try {

            DB::beginTransaction();

            foreach ($this->getElementsToDestroy() as $element) {
                $element->destroyElement();
            }


            if ($this->send_event) {
                $this->post_events_to_send =
                    Evt\Type\ElementDestroyed::makeEventActions(
                        source: $this, action_data: $this->action_data,important_array: $this->getElementsToDestroy());
            }


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }



    protected function getMyData() :array {
        return [
            'destroyed_elements'=>$this->getElementsToDestroy()
        ];
    }

    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret = [];
        if (isset($what['destroyed_elements'])) {
            $ret['destroyed_elements'] = new ElementCollectionResponse(given_elements:  $what['given_elements']);
        }

        return $ret;
    }



    public function getChildrenTree(): ?Tree
    {

        if ($this->send_event && !$this->is_system) {
            $nodes = [];
            $owner_events = Evt\Type\ElementDestruction::makeEventActions(source: $this, action_data: $this->action_data,
                important_array: $this->getElementsToDestroy());


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


        if ($child instanceof Evt\Type\ElementDestruction) {
            if ($child->isActionError() || $child->isActionFail()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            } else if ($child->isActionSuccess()) {
                $this->setFlag(TypeOfFlag::CAN_WRITE,true);
            }
        }


    }

}

