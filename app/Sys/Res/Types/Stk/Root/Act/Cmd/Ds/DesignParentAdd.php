<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;


use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Attributes\TypeOfServerAccess;
use App\Enums\Sys\TypeOfAction;
use App\Enums\Types\TypeOfApproval;
use App\Exceptions\HexbatchFailException;
use App\Exceptions\RefCodes;
use App\Models\ActionDatum;
use App\Models\ElementType;
use App\Models\ElementTypeParent;

use App\Models\UserNamespace;
use App\OpenApi\Types\TypeResponse;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use BlueM\Tree;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\DB;

#[HexbatchTitle( title: "Add a parent to the type")]
#[HexbatchBlurb( blurb: "Parents can be given to types in design mode. Some parents have to agree")]
#[HexbatchDescription( description:'')]
class DesignParentAdd extends Act\Cmd\Ds
{
    const UUID = '362a3cdf-f013-4bc0-afce-315cba179544';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_PARENT_ADD;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\DesignPending::class
    ];

    public function getDesignType(): ?ElementType
    {
        return $this->getGivenType();
    }

    protected function setDesignType(ElementType $type) : void {
        $this->given_type_uuid = $type->ref_uuid;
        $this->action_data->collection_data =$this->getInitialConstantData();
        $this->setGivenType($type,true);;
    }

    protected function addToParents(ElementType $type) : void {
        $this->given_parent_uuids = array_unique( array_merge($this->given_parent_uuids,[$type->ref_uuid]) );
        $this->saveCollectionKeys();
        $this->action_data->collection_data =$this->getInitialConstantData();
        $this->action_data->save();
    }


    /**
     * @return ElementType[]
     */
    public function getParents(): array
    {
        return $this->action_data->getCollectionOfType(ElementType::class);
    }

    const array ACTIVE_DATA_KEYS = ['given_type_uuid','given_parent_uuids','check_permission'];

    const array ACTIVE_COLLECTION_KEYS = ['given_parent_uuids'=>ElementType::class];
    public function __construct(
        protected ?string              $given_type_uuid = null,
        /**
         * @var string[] $given_parent_uuids
         */
        protected array               $given_parent_uuids = [],
        protected ?TypeOfApproval     $approval = null,
        protected bool                $check_permission = true,
        protected bool                $is_system = false,
        protected bool                $send_event = true,
        protected ?ActionDatum        $action_data = null,
        protected ?ActionDatum        $parent_action_data = null,
        protected ?UserNamespace      $owner_namespace = null,
        protected bool                $b_type_init = false,
        protected ?bool                $is_async = null,
        protected array             $tags = []
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
        if (!$this->getDesignType()) {
            throw new \InvalidArgumentException("Need type before can add parents to it");
        }

        if ($this->check_permission) {
            $this->checkIfAdmin($this->getDesignType()->owner_namespace);
        }


        try {
            DB::beginTransaction();
            if ( $this->approval && $this->approval !== TypeOfApproval::APPROVAL_NOT_SET ) {
                //manually make this set for all parents when creating them, else they are created in the child answers
                foreach ($this->getParents() as $parent) {
                    if (!$this->is_system) {
                        if (is_subclass_of($parent , Act\SystemPrivilege::class )) {
                            throw new \RuntimeException("Non system types cannot have system-privilege as a parent"); //
                        }
                        if (is_subclass_of($parent , Act\NoEventsTriggered::class )) {
                            throw new \RuntimeException("Non system types cannot have no-events as a parent"); //
                        }

                        if (is_subclass_of($parent , Act\CmdNoSideEffects::class )) {
                            throw new \RuntimeException("Non system types cannot have no-events as a parent"); //
                        }
                    }
                    $b_check_parent = true;
                    if ($this->is_system && !$this->send_event) { $b_check_parent = false;}
                    ElementTypeParent::addOrUpdateParent(parent: $parent, child: $this->getDesignType(), approval: $this->approval,check_parent_published: $b_check_parent);
                }
            }


            //public domain parents are automatically approved for the design
            foreach ($this->getParents() as $parent) {
                if ($this->is_system ||$parent->isPublicDomain()
                    || !$this->check_permission ||$parent->owner_namespace->isNamespaceAdmin($this->getNamespaceInUse())) {
                    ElementTypeParent::addOrUpdateParent(parent: $parent, child: $this->getDesignType(),
                        approval: TypeOfApproval::DESIGN_APPROVED,check_parent_published:!$this->is_system );
                }
            }


            //check to see if all parents have approved this design, if so then success, else fail
            /** @var ElementTypeParent[] $check_parents */
            $check_parents = ElementTypeParent::buildTypeParents(child_type_id: $this->getDesignType()->id)->get();

            foreach ($check_parents as $checker) {
                if ($checker->parent_type_approval === TypeOfApproval::DESIGN_DENIED) {

                    throw new HexbatchFailException( __('msg.design_parents_did_not_approve_design',['ref'=>$checker->getName(),
                        'child'=>$this->getDesignType()->getName()]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::TYPE_PARENT_DENIED_DESIGN);
                }
            }

            DB::commit();
        }

        catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }




    protected function getMyData() :array {
        return ['type'=>$this->getDesignType()];
    }

    public function getDataSnapshot(): array
    {
        $ret = [];
        $what =  $this->getMyData();
        if (isset($what['type'])) {
            $ret['type'] = new TypeResponse(given_type: $what['type'],parent_levels: 1);
        }
        return $ret;
    }

    protected function restoreData(array $data = []) {
        parent::restoreData($data);
        if ($this->action_data) {
            if ($this->action_data->collection_data?->offsetExists('approval')) {
                $approval_string = $this->action_data->collection_data->offsetGet('approval');
                $this->approval = TypeOfApproval::tryFromInput($approval_string);
            }

        }
    }


    protected function initData(bool $b_save = true) : ActionDatum {
        parent::initData(b_save: false);
        $this->setGivenType($this->given_type_uuid);
        $this->action_data->collection_data->offsetSet('approval',$this->approval?->value);
        $this->action_data->save();
        $this->action_data->refresh();
        return $this->action_data;
    }

    public function getInitialConstantData(): array {
        $ret = parent::getInitialConstantData();
        $ret['approval'] = $this->approval?->value;
        return $ret;
    }


    public function getChildrenTree(): ?Tree
    {

        if ($this->send_event) {
            $events = [];
            $nodes = [];
            foreach ($this->getParents() as $parent) {
                if (
                    !
                    ($this->is_system || $parent->isPublicDomain() || !$this->check_permission ||$parent->owner_namespace->isNamespaceAdmin($this->getNamespaceInUse())  )
                )
                {
                    $events =  Evt\Server\DesignPending::makeEventActions(source: $this, action_data: $this->action_data,type_context: $parent);
                }

            }
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


        if ($child instanceof Evt\Server\DesignPending) {
            if ($child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else {
                if ($this->given_type_uuid === $child->getAskedAboutType()?->ref_uuid) {
                    if ($child->isActionSuccess()) {
                        if (in_array($child->getParentType()->ref_uuid, $this->given_parent_uuids)) {
                            if (!$this->is_system) {
                                if (is_subclass_of($child->getParentType(), Act\SystemPrivilege::class)) {
                                    throw new \RuntimeException("Non system types cannot have system-privilege as a parent"); //
                                }
                                if (is_subclass_of($child->getParentType(), Act\NoEventsTriggered::class)) {
                                    throw new \RuntimeException("Non system types cannot have no-events as a parent"); //
                                }
                            }

                            ElementTypeParent::addOrUpdateParent(
                                parent: $child->getParentType(), child: $this->getDesignType(), approval: $child->getApprovalStatus());
                        }
                    } else if($child->isActionFail()) {
                        ElementTypeParent::addOrUpdateParent(
                            parent: $child->getParentType(), child: $this->getDesignType(), approval: TypeOfApproval::DESIGN_DENIED);
                    }

                }
            }
        } //end if this is design pending

        if ($child instanceof Act\Cmd\Ds\DesignCreate) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            } else if($child->isActionSuccess()) {
                if ($child->getGivenType()) {
                    $this->setDesignType(type: $child->getGivenType());
                }
            }
        }

        if ($child instanceof Act\Cmd\Ty\TypePublish) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            } else if($child->isActionSuccess()) {
                if ($child->getPublishingType()) {
                    $this->addToParents(type: $child->getPublishingType());
                }
            }
        }

    }

}

