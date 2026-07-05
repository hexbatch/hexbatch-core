<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\St;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\Sets\Params\AddElementsParamData;
use App\Enums\Sys\TypeOfAction;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementSetMember;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

#[HexbatchTitle( title: "Add elements to set")]
#[HexbatchBlurb( blurb: "Add one or more elements to a set")]
#[HexbatchDescription( description: /** @lang markdown */
    '
# Create elements

  This adds elements to a set, the set and elements must be already existing.

  is_sticky: if the elements are sticky, remaining after the remove command

  Creation can be blocked by the following:

  By the set

  * [SetEntering.php](../../../Evt/Set/SetEntering.php)


  After the elements are added, the following notices are given

   * [SetEntered.php](../../../Evt/Set/SetEntered.php)
   * [ShapeEntered.php](../../../Evt/Set/ShapeEntered.php)
   * [MapEntered.php](../../../Evt/Set/MapEntered.php)
   * [TypeMapEnclosedStart.php](../../../Evt/Set/TypeMapEnclosedStart.php)
   * [TypeShapeEnclosedStart.php](../../../Evt/Set/TypeShapeEnclosedStart.php)


')]
#[ApiParamMarker( param_class: AddElementsParamData::class)]

class SetMemberAdd extends Act\Cmd\St
{
    const UUID = 'ebd1275e-ecc6-486e-89cb-69e14ae4a44c';
    const ACTION_NAME = TypeOfAction::CMD_SET_MEMBER_ADD;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\St::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\SetEntering::class,
        Evt\Set\SetEntered::class,
        Evt\Set\ShapeEntered::class,
        Evt\Set\MapEntered::class,
        Evt\Set\TypeMapEnclosedStart::class,
        Evt\Set\TypeShapeEnclosedStart::class,
    ];



    public function __construct(
        protected AddElementsParamData     $params,
        protected ElementSet                $given_set,
        protected bool                      $is_system,
        protected UserNamespace             $calling_namespace,
        protected ?Collection $selected_elements = null

    )
    {
        if (!$this->selected_elements) {
            $this->params->selection->phase_ref = $this->given_set->defining_element->element_phase->ref_uuid;
            $this->selected_elements = Element::getElementsFromParams(
                params: $this->params->selection, b_ns_relations: true, b_type_relations: true, b_ns_type_relations: false,
                not_member_set_id: $this->given_set->id,cursor: $this->params->selection->cursor);

        }

    }

    protected  function toArray() :array {
        return [
            'params'=>$this->params->toArray(),
            'given_set'=>$this->given_set,
            'selected_elements'=>$this->selected_elements,
            'is_system'=>$this->is_system,
            'calling_namespace'=>$this->calling_namespace,
        ];
    }
    protected static function fromArray(array $args) : static{
        $params = AddElementsParamData::from($args['params']);
        $is_system = (bool)$args['is_system'];
        $given_set = static::getSetFromArray('given_set',$args);
        $selected_elements = static::getElementCollectionFromArray('selected_elements',$args);
        $calling_namespace = static::getNamespaceFromArray('calling_namespace',$args);
        return new static(params: $params,given_set: $given_set,is_system: $is_system,calling_namespace: $calling_namespace,selected_elements: $selected_elements);
    }


    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        if ($b_approved) {
            $added_members = $work->addElements();
            foreach ($added_members as $e) {
                $work->fireNotificationsForElement($e,$children_args);
            }
        } else {
            $added_members = [];
        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,
            data: [static::CHILD_DECISION_KEY =>$b_approved,'members'=>$added_members]);
    }



    /**
     * @return Collection<ElementSetMember>
     * @throws
     */
    protected function addElements() {
        $ids = [];
        //anyone can add any element they can see
        try {
            DB::beginTransaction();
            foreach ($this->selected_elements as $element) {
                $member = $this->given_set->addElement(ele: $element,is_sticky: $this->params->is_sticky);
                $ids[] = $member->id;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        if (count($ids)) {
            return ElementSetMember::buildSetMember(given_ids: $ids)->get();
        }

        return new Collection;
    }

    protected function fireNotificationsForElement(ElementSetMember $e,array $children_args) {
        $callables = [
            Evt\Set\SetEntered::class,
            Evt\Set\ShapeEntered::class,
            Evt\Set\MapEntered::class,
            Evt\Set\TypeMapEnclosedStart::class,
            Evt\Set\TypeShapeEnclosedStart::class,
        ];

        foreach ($callables as $callable_class) {
            $r = new $callable_class($this->given_set,$e->of_element);
            $r->callTreeByItself($children_args);
        }
    }


    /**
     * @throws \Throwable
     */
    public static function createSetAddTree(
         AddElementsParamData     $params,
         ElementSet                $given_set,
         bool                      $is_system,
         UserNamespace             $calling_namespace,
        ?IThangBuilder              $builder = null
    ) : Thang|IThangBuilder
    {

        if (!$is_system) {
            $given_set->loadMissing('defining_type');
            $given_set->loadMissing('defining_type.owner_namespace');
            static::checkIfGivenIsAdmin(given: $calling_namespace,target: $given_set->defining_type->owner_namespace);
        }

        $me = new static(
            params: $params,
            given_set: $given_set,
            is_system: $is_system,
            calling_namespace: $calling_namespace
        );

        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $builder?: $builder = ThangBuilder::createBuilder();
        $builder->setNamespace($calling_namespace);





        $builder->tree(
            command_class: static::class,
            command_args: $me->toArray(),
            command_tags: [static::class],
        );

        if (!$is_system)
        {

            foreach ($me->selected_elements as $el) {
                Evt\Set\SetEntering::callEventTree(
                    given_set: $given_set,
                    given_element: $el,
                    builder: $builder);
            }
        }



        if ($ret_builder) {
            return $builder;
        }

        $thang = $builder->execute()->getThang();
        return $thang;
    }

}

