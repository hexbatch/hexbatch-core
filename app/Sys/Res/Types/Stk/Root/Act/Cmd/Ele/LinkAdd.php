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
use App\Models\ElementLink;
use App\Models\ElementSet;
use App\Models\ElementType;
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


#[HexbatchTitle( title: "Add a link")]
#[HexbatchBlurb( blurb: "Can link a set with an element")]
#[HexbatchDescription( description: /** @lang markdown */
    '
# Linking sets

Creates groups of sets or organize and do batch actions.

given_set_uuid: the set being the target
given_element_uuid: the element being the anchor

Any set can be linked, if no event handler for the element,
then only permission check is that the calling namespace is in element admin group

The element and set types  will recieve a

   * [LinkCreating](../../../Evt/Server/LinkCreating.php)

If all report back ok, then the link is made.

Once the link is made, the element and type owners, and set, will get an event
   * [LinkCreated](../../../Evt/Server/LinkCreated.php)


')]
#[ApiParamMarker( param_class: SelectElementParamData::class)]
#[ApiEventMarker( Evt\Element\LinkCreated::class)] //post
#[ApiEventMarker( Evt\Element\LinkCreating::class)] //post

class LinkAdd extends Act\Cmd\Ele implements ICommandCallable
{
    const UUID = '6eaef3f7-a458-459f-85aa-75d863677101';
    const ACTION_NAME = TypeOfAction::CMD_LINK_ADD;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

    const EVENT_CLASSES = [
        Evt\Element\LinkCreated::class,
        Evt\Element\LinkCreating::class,
    ];


    public function __construct(
        protected ?SelectElementParamData $params,
        protected  ElementSet                  $given_set,
        protected bool                    $is_system,
        protected UserNamespace           $calling_namespace,

        /** @var Collection<Element>|null        $selected_elements */
        protected ?Collection             $selected_elements = null,


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
            'given_set'=> $this->given_set,
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
        $given_set = static::getSetFromArray('given_set',$args);
        return new static(
            params: $params,
            given_set: $given_set, is_system: $is_system,
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
            $work->doLinkOfElements();
            foreach ($work->selected_elements as $e) {
                $work->fireNotificationsForElement(e:$e,s:$work->given_set,children_args: $children_args);
            }
        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,
            data: [static::CHILD_DECISION_KEY =>$b_approved,'elements'=>$work->selected_elements->toArray()]);
    }


    /**
     * @throws \Throwable
     */
    protected function doLinkOfElements()
    : void
    {
        if ($this->is_system) {
            static::checkPermissions(given_elements: $this->selected_elements, calling_namespace: $this->calling_namespace);
        }

        DB::transaction(function() {
            foreach ($this->selected_elements as $el) {
                ElementLink::makeLink(el: $el,set: $this->given_set);
            }
        });


    }

    /**
     * @throws \Throwable
     */
    protected function fireNotificationsForElement(Element $e, ?ElementSet $s, array $children_args) {
        $callables = [
            Evt\Element\LinkCreated::class
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
    public static function linkAddTree(
        ?SelectElementParamData    $params,
        ElementSet                $given_set,
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

        $me = new static(
            params: $params,
            given_set: $given_set,
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
                Evt\Element\LinkCreating::callEventTree(
                    given_element: $el,
                    given_set: $given_set,
                    builder: $builder);
            }
        }



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

