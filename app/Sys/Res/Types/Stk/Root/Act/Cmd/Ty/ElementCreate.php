<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Exceptions\HexbatchNothingDoneException;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\ActionDatum;
use App\Models\Element;
use App\Models\ElementType;
use App\Models\Phase;
use App\Models\UserNamespace;
use App\OpenApi\Elements\ElementCollectionResponse;
use App\OpenApi\Params\Type\CreateElementParams;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\DB;


#[HexbatchTitle( title: "Create elements")]
#[HexbatchBlurb( blurb: "Create one or more elements from a type")]
#[HexbatchDescription( description: /** @lang markdown */
    '
# Create elements

  This can create one or many elements at once, must be from the same type.

  If no handler for element creation, then only the type admin members can create

  given_type_uuid: uuid of the type
  given_namespace_uuid: uuid of the namespace to put the element into, if not given, the same namespace as the call will be used
  given_phase_uuid: uuid of the phase, if not given, the default will be used
  number_to_create: if missing will be one


  Creation can be blocked by the following:

  By the type owners who get

  * [ElementCreation.php](../../../Evt/Type/ElementCreation.php)

  By the recipients who get

   * [ElementOwnerChange](../../../Evt/Type/ElementOwnerChange.php)




  After element creation the recipent gets a notice

  * [ElementRecieved](../../../Evt/Type/ElementRecieved.php)


')]
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
        Evt\Type\ElementOwnerChange::class,
        Evt\Type\ElementRecieved::class,
        Evt\Type\ElementRecievedBatch::class
    ];

    /**
     * @return Element[]
     */
    public function getElementsCreated(): array
    {
        return $this->action_data->getCollectionOfType(Element::class);
    }

    public function getNamespaceUsed(): ?UserNamespace
    {
        return $this->getGivenNamespace();
    }

    public function getPhaseUsed(): ?Phase
    {
        /** @uses ActionDatum::data_phase() */
        return $this->action_data->data_phase;
    }

    protected function setTemplateType(ElementType $type) : void {
        $this->given_type_uuid = $type->ref_uuid;
        $this->action_data->collection_data =$this->getInitialConstantData();
        $this->setGivenType($type,true);
    }

    public function getTemplateType(): ?ElementType
    {
        return $this->getGivenType();
    }

    public function setNumberToMake(int $number_allowed) : void {
        $this->number_to_create = min($number_allowed,$this->number_to_create);
        $this->action_data->collection_data =$this->getInitialConstantData();
        $this->action_data->save();
    }

    /**
     * @var string[] $created_element_uuids
     */
    protected array $created_element_uuids = [];

    const array ACTIVE_COLLECTION_KEYS = ['created_element_uuids'=>Element::class];

    const array ACTIVE_DATA_KEYS = ['given_type_uuid','given_namespace_uuid','given_phase_uuid',
        'number_to_create','preassinged_uuids','b_must_have_namespace'];

    #[ApiParamMarker( param_class: CreateElementParams::class)]
    public function __construct(
        protected ?string       $given_type_uuid = null,
        protected ?string       $given_namespace_uuid = null,
        protected ?string      $given_phase_uuid = null,
        protected int          $number_to_create = 0,
        protected array        $preassinged_uuids = [],
        protected bool         $b_must_have_namespace = true,
        protected bool         $is_system = false,
        protected bool         $send_event = true,
        protected ?bool                $is_async = null,
        protected ?ActionDatum $action_data = null,
        protected ?ActionDatum        $parent_action_data = null,
        protected ?UserNamespace      $owner_namespace = null,
        protected bool         $b_type_init = false,
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

        if ($this->b_must_have_namespace && !$this->getNamespaceUsed()) {
            throw new \InvalidArgumentException("Need namespace before can make element");
        }

        if (!$this->getTemplateType()) {
            throw new \InvalidArgumentException("Need template type before can make element");
        }

        if (!$this->getTemplateType()->isPublished()) {
            throw new HexbatchNotPossibleException(__("msg.type_must_be_published_before_making_elements",
                ['ref' => $this->getTemplateType()->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::TYPE_NEEDS_PUBLISHING);
        }


        if ($this->number_to_create <= 0) {
            throw new HexbatchNothingDoneException(__("msg.type_given_zero_elements_to_make",
                ['ref' => $this->getTemplateType()->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::TYPE_GIVEN_ZERO_TO_MAKE);
        }



        try {
            $this->created_element_uuids = [];
            $uuid_index = 0;

            DB::beginTransaction();

            for ($set_index = 0; $set_index < $this->number_to_create; $set_index++) {
                $this->makeElement(loop_number: $uuid_index++);
            } //end non set creation


            if (count($this->created_element_uuids) > 1 ) {
                if ($this->send_event) {
                    $this->post_events_to_send =
                        Evt\Type\ElementRecievedBatch::makeEventActions(
                            source: $this, action_data: $this->action_data,important_array: $this->getElementsCreated());
                }
            } else if(count($this->created_element_uuids) === 1) {
                if ($this->send_event) {
                    $this->post_events_to_send =
                        Evt\Type\ElementRecieved::makeEventActions(
                            source: $this, action_data: $this->action_data,important_array: $this->getElementsCreated());
                }
            }



            $this->saveCollectionKeys();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }


    private function makeElement(int $loop_number) : void
    {

        $phase_id = $this->getPhaseUsed()?->id;
        $namespace_owner_id = $this->getNamespaceUsed()?->id;
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
    }



    protected function getMyData() :array {
        return [
            'created_elements'=>$this->getElementsCreated(),'template_type'=>$this->getTemplateType(),
            'namespace_used'=>$this->getNamespaceUsed(),'phase_used'=>$this->getPhaseUsed()];
    }

    public function getDataSnapshot(): array
    {
        $what =  $this->getMyData();
        $ret = [];
        if (isset($what['created_elements'])) {
            $ret['created_elements'] = new ElementCollectionResponse(given_elements:  $what['created_elements']);
        }

        return $ret;
    }



    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);

        $this->setGivenNamespace( $this->given_namespace_uuid)->setGivenType($this->given_type_uuid);

        if ($this->given_phase_uuid) {
            $this->action_data->data_phase_id = Phase::getThisPhase(uuid: $this->given_phase_uuid)->id;
        } else {
            $this->action_data->data_phase_id = Phase::getDefaultPhase()?->id;
        }

        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }

    public function getChildrenTree(): ?Tree
    {

        if ($this->send_event && !$this->is_system) {
            $nodes = [];

            $creation_events = Evt\Type\ElementCreation::makeEventActions(source: $this, action_data: $this->action_data,
                type_context: $this->getTemplateType());

            $owner_events = Evt\Type\ElementOwnerChange::makeEventActions(source: $this, action_data: $this->action_data,
                type_context: $this->getTemplateType());

            $events = array_merge($creation_events,$owner_events);

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

    /**
     * @throws \Exception
     */
    public function setChildActionResult(IThingAction $child): void {


        if ($child instanceof Evt\Type\ElementCreation) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }

            else if($child->isActionSuccess()) {
                if ($child->getAskedAboutType() === $this->getTemplateType()) {
                    $this->setNumberToMake($child->getNumberAllowed());
                }
            }
        }


        if ($child instanceof Evt\Type\ElementOwnerChange ) {
            if ($child->isActionError() || $child->isActionFail()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
        }


        if ($child instanceof TypePublish) {
            if ($child->isActionError() || $child->isActionFail()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else if($child->isActionSuccess() && $child->getPublishingType()) {
                $this->setTemplateType(type: $child->getPublishingType());
            }
        }

    }

}

