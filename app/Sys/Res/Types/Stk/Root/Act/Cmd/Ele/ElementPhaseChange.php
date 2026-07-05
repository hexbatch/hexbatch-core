<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Annotations\ApiEventMarker;
use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\Elements\Params\SelectElementParamData;
use App\Enums\Sys\TypeOfAction;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\Phase;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

#[HexbatchTitle( title: "Change element ownership")]
#[HexbatchBlurb( blurb: "Give one or more elements to a new owner, they can be mixed types")]
#[HexbatchDescription( description: /** @lang markdown */
    '
# Change element phase via path

  This changes the phase of one or more elements without asking permission or emitting events except for its own post event



  After element phase change, the type owner gets a notice

  * [ElementRecieved](../../../Evt/Type/PhaseChangedQuiet.php)


')]
#[ApiEventMarker( Evt\Type\PhaseChangedQuiet::class)] //pre
#[ApiParamMarker( param_class: SelectElementParamData::class)]
class ElementPhaseChange extends Act\Cmd\Ele implements ICommandCallable
{
    const UUID = '28af2e60-d1a9-43c5-bb25-a1ab445b8b1a';
    const ACTION_NAME = TypeOfAction::CMD_ELEMENT_CHANGE_OWNER;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

    const EVENT_CLASSES = [
        Evt\Type\PhaseChangedQuiet::class,
    ];


    public function __construct(
        protected ?SelectElementParamData $params,
        protected  Phase                 $given_phase,
        protected bool                   $is_system,
        protected UserNamespace          $calling_namespace,

        /** @var Collection<Element>|null        $selected_elements */
        protected ?Collection            $selected_elements = null,


    )
    {
        if (!$this->selected_elements && $this->params) {
            $this->selected_elements = Element::getElementsFromParams(
                params: $this->params, b_ns_relations: true, b_type_relations: true, b_ns_type_relations: false,cursor: $this->params->cursor);
        }

    }



    protected  function toArray() :array {
        return [
            'params'=>$this->params?->toArray(),
            'is_system'=> $this->is_system,
            'given_phase'=> $this->given_phase,
            'calling_namespace'=> $this->calling_namespace,
            'selected_elements'=> $this->selected_elements,
        ];
    }
    protected static function fromArray(array $args) : static{
        $params = null;
        if (!empty($args['params']??null)) {
            $params = SelectElementParamData::from($args['params']);
        }

        $is_system = (bool)$args['is_system'];
        $given_elements = static::getElementCollectionFromArray('selected_elements',$args);
        $calling_namespace = static::getNamespaceFromArray('calling_namespace',$args);
        $given_phase = static::getPhaseFromArray('given_phase',$args);
        return new static(
            params: $params,
            given_phase: $given_phase, is_system: $is_system,
            calling_namespace: $calling_namespace, selected_elements: $given_elements);
    }

    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        if ($b_approved) {
            $moved_elements = $work->changePhaseOfElements();
            foreach ($work->selected_elements as $e) {
                $work->fireNotificationsForElement(e:$e,s:null,children_args: $children_args);
            }
        } else {
            $moved_elements = new Collection();
        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,
            data: [static::CHILD_DECISION_KEY =>$b_approved,'elements'=>$moved_elements->toArray()]);
    }


    /** @return Collection<Element>
     * @throws \Throwable
     */
    protected function changePhaseOfElements()
    : Collection
    {
        if ($this->is_system) {
            static::checkPermissions(given_elements: $this->selected_elements, calling_namespace: $this->calling_namespace);
        }

        DB::transaction(function() {
            foreach ($this->selected_elements as $el) {
                $el->element_phase_id = $this->given_phase->id;
                $el->save();
            }
        });


        return $this->selected_elements;

    }

    /**
     * @throws \Throwable
     */
    protected function fireNotificationsForElement(Element $e, ?ElementSet $s, array $children_args) {
        $callables = [
            Evt\Type\PhaseChangedQuiet::class
        ];

        foreach ($callables as $callable_class) {
            $r = new $callable_class($e->of_element,$s);
            $r->callTreeByItself($children_args);
        }
    }

    /**
     * @param Collection<Element> $given_elements
     */
    protected static function checkPermissions(Collection  $given_elements, UserNamespace  $calling_namespace)
    {
        $ns = [];
        foreach ($given_elements as $el) {
            $ns[$el->element_namespace->ref_uuid] = $el->element_namespace;
        }
        foreach ($ns as $a_ns) {
            static::checkIfGivenIsAdmin(given: $calling_namespace,target: $a_ns);
        }
    }


    /**
     * @throws \Throwable
     */
    public static function changeElementPhaseTree(
         ?SelectElementParamData    $params,
         Phase                      $given_phase,
         bool                      $is_system,
         UserNamespace             $calling_namespace,

        /** @var Collection<Element>        $given_elements */
         Collection|null                $given_elements = null,
         ?IThangBuilder $builder = null
    ) : ElementType|Thang|IThangBuilder
    {

        if (!$is_system) {
            static::checkPermissions(given_elements: $given_elements, calling_namespace: $calling_namespace);
        }

        $node = new static(
            params: $params,
            given_phase: $given_phase,
            is_system: $is_system,
            calling_namespace: $calling_namespace,
            selected_elements: $given_elements
        );

        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $builder?: $builder = ThangBuilder::createBuilder();
        $builder->setNamespace($calling_namespace);


        $builder->tree(
            command_class: static::class,
            command_args: $node->toArray(),
            command_tags: [static::class],
        );



        if ($ret_builder) {
            return $builder;
        }

        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            return  ElementType::getElementType(uuid: $data['ref_uuid']);
        } else {
            return $thang;
        }
    }


}

