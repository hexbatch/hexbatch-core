<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\Elements\Params\SelectElementParamData;
use App\Enums\Sys\TypeOfAction;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementVisibility;
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
   * [SwitchingOff](../../../Evt/Set/SwitchingOff.php)

   if all agree, then the type is turned off for that element

   and the element owner and type owners, and anyone else listening gets the following

   * [SwitchedOff](../../../Evt/Set/SwitchedOff.php)
')]
#[ApiParamMarker( param_class: SelectElementParamData::class)]
class SwitchOff extends Act\Cmd\Ele
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
        Evt\Set\SwitchingOff::class,
        Evt\Set\SwitchedOff::class,
    ];

    const bool SWITCH_ON = false;

    const PRE_EVENT_CLASS = Evt\Set\SwitchingOff::class;
    const POST_EVENT_CLASS = Evt\Set\SwitchedOff::class;

    public function __construct(
        protected SelectElementParamData     $params,

        protected bool                      $is_system,
        protected UserNamespace             $calling_namespace,

        /** @var Collection<Element> $selected_elements */
        protected ?Collection $selected_elements = null,

    )
    {
        if (!$this->selected_elements) {
            $this->selected_elements = $this->getSelectedElements();
        }

        if (!$this->is_system) {
            $this->checkPermissions();
        }


    }

    protected  function toArray() :array {
        return [
            'params'=>$this->params->toArray(),
            'selected_elements'=>$this->selected_elements,
            'is_system'=>$this->is_system,
            'calling_namespace'=>$this->calling_namespace,
        ];
    }
    protected static function fromArray(array $args) : static{
        $params = SelectElementParamData::from($args['params']);
        $is_system = (bool)$args['is_system'];
        $selected_elements = static::getElementCollectionFromArray('selected_elements',$args);
        $calling_namespace = static::getNamespaceFromArray('calling_namespace',$args);
        return new static(params: $params,is_system: $is_system,calling_namespace: $calling_namespace,selected_elements: $selected_elements);
    }


    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        $given_set = null;
        if ($b_approved) {
            $work->switchOnOrOff();
            if ($work->params->set_ref) {
                $given_set = ElementSet::getThisSet(uuid:$work->params->set_ref);
            }
            foreach ($work->selected_elements as $e) {
                $work->fireNotificationsForElement(e:$e,s:$given_set,children_args: $children_args);
            }
        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,
            data: [static::CHILD_DECISION_KEY =>$b_approved,'elements'=>$work->selected_elements,'set'=>$given_set]);
    }

    /**
     * @return Collection<Element>
     */
    protected function getSelectedElements() : Collection {
        //gets elements from params

        return  Element::getElementsFromParams(
            params: $this->params, b_ns_relations: true, b_type_relations: true, b_ns_type_relations: false,cursor: $this->params->cursor);
    }

    protected function checkPermissions()
    {
        //hash to same owner groups
        $owners = [];
        foreach ($this->selected_elements as $el) {
            $ns = $el->element_namespace;
            if (!isset($owners[$ns->ref_uuid])) {
                $owners[$ns->ref_uuid] = $ns;
            }
        }

        foreach ($owners as $ns_to_check) {
            static::checkIfGivenIsAdmin(given: $this->calling_namespace,target: $ns_to_check);
        }
    }


    /**
     * @throws
     */
    protected function switchOnOrOff() {

        try {

            DB::beginTransaction();
            ElementVisibility::switchVisibility(
                params: $this->params,
                is_turned_on: static::SWITCH_ON
            );


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

    /**
     * @throws \Throwable
     */
    protected function fireNotificationsForElement(Element $e, ?ElementSet $s, array $children_args) {
        $callables = [
            static::POST_EVENT_CLASS
        ];

        foreach ($callables as $callable_class) {
            $r = new $callable_class($e->of_element,$s);
            $r->callTreeByItself($children_args);
        }
    }


    /**
     * @throws \Throwable
     */
    public static function createSwitchTree(
        SelectElementParamData     $params,
        bool                      $is_system,
        UserNamespace             $calling_namespace,
        ?Collection $selected_elements = null,
        ?IThangBuilder              $builder = null
    ) : Thang|IThangBuilder
    {



        $me = new static(
            params: $params,
            is_system: $is_system,
            calling_namespace: $calling_namespace,
            selected_elements: $selected_elements
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
            $given_set = null;
            if ($params->set_ref) {
                $given_set = ElementSet::getThisSet(uuid:$params->set_ref);
            }
            foreach ($me->selected_elements as $el) {
                static::PRE_EVENT_CLASS::callEventTree(
                    given_element: $el,
                    given_set: $given_set,
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

