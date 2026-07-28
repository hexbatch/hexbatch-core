<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Annotations\ApiEventMarker;
use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\Elements\Params\SelectElementParamData;
use App\Enums\Sys\TypeOfAction;
use App\Exceptions\HexbatchNothingDoneException;
use App\Exceptions\RefCodes;
use App\Models\Element;
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

 It is ok if an element is destroyed while thangs are working on it. They will fail, or they will finish without it.

')]
#[ApiEventMarker( Evt\Type\ElementDestruction::class)]
#[ApiEventMarker( Evt\Type\ElementDestroyed::class)]
class ElementDestroy extends Act\Cmd\Ele implements ICommandCallable
{
    const UUID = '557bbc2e-f589-4874-91f0-5d5e96fe115f';
    const ACTION_NAME = TypeOfAction::CMD_ELEMENT_DESTROY;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

    const EVENT_CLASSES = [
        Evt\Type\ElementDestruction::class,
        Evt\Type\ElementDestroyed::class
    ];

    #[ApiParamMarker( param_class: SelectElementParamData::class)]
    public function __construct(

        protected SelectElementParamData $params,
        /** @param Collection<Element> $elements */
        protected ?Collection $elements,
        protected UserNamespace $caller_namespace,
        protected bool          $is_system
    )
    {
        if (!$this->elements) {
            $this->elements = Element::getElementsFromParams(params: $this->params,
                b_ns_relations: true,b_type_relations: true,b_ns_type_relations: true,cursor: $this->params->cursor);

            if (count($this->elements)  <= 0) {
                throw new HexbatchNothingDoneException(__("msg.elements_mising_from_destroy_list"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ELEMENTS_NOT_LISTED_TO_DESTROY);
            }
        }

    }


    protected  function toArray() :array {
        return [
            'is_system'=>$this->is_system,
            'elements'=>$this->elements->toArray(),
            'caller_namespace'=>$this->caller_namespace,
            'params'=>$this->params->toArray(),
        ];
    }

    protected static function fromArray(array $args) : static {
        $is_system = (bool)$args['is_system'];
        $params = SelectElementParamData::from($args['params']);
        $caller_namespace =  static::getNamespaceFromArray('caller_namespace',$args) ;
        $elements = static::getElementCollectionFromArray('elements',$args );
        return new static(params: $params, elements: $elements,caller_namespace: $caller_namespace,is_system: $is_system);
    }


    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        if ($b_approved) {
            $refs_deleted = $work->doElementDeletions();
        } else {
            $refs_deleted = [];
        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,
            data: [static::CHILD_DECISION_KEY =>$b_approved,'refs_deleted'=>$refs_deleted]);
    }


    /**
     * @throws \Throwable
     */
    protected function doElementDeletions() : array  {
        if ($this->is_system) {
            $this->checkForPermissions();
        }
        $ret = [];
        foreach ($this->elements as $el) {
            $ret[] = $el->ref_uuid;
        }
        DB::transaction(function() use($ret) {
            Element::whereIn('ref_uuid',$ret)->delete();
        });

        return $ret;
    }

    protected  function checkForPermissions() {

        $ns = [];
        foreach ($this->elements as $el) {
            $ns[$el->element_namespace->ref_uuid] = $el->element_namespace;
        }

        foreach ($ns as $target_namespace) {
            static::checkIfGivenIsAdmin(given: $this->caller_namespace,target: $target_namespace);
        }

    }




    /**
     * @throws \Throwable
     */
    public static function destroyElements(
        SelectElementParamData $params, bool $is_system,
        UserNamespace $caller_namespace,
        ?IThangBuilder $builder = null
    ) : Collection|Thang|IThangBuilder
    {

        $me = new static(params: $params,elements: null,caller_namespace: $caller_namespace,is_system: $is_system);
        if (!$is_system) {
            $me->checkForPermissions();
        }



        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $builder?: $builder = ThangBuilder::createBuilder();
        $builder->setNamespace($caller_namespace);



        $builder->tree(
            command_class: Evt\Type\ElementDestroyed::class,
            command_args: new Evt\Type\ElementDestroyed(
                elements: $me->elements
            )->toArray(),
            command_tags: [Evt\Type\ElementDestroyed::class]
        );

        $builder->tree(
            command_class: static::class,
            command_args: $me->toArray(),
            command_tags: [static::class],
            command_priority: -1
        );

        if (!$is_system) {
            Evt\Type\ElementDestruction::callEventTree(elements: $me->elements,builder: $builder);
        }



        if ($ret_builder) {
            return $builder;
        }

        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            return  $me->elements;
        } else {
            return $thang;
        }
    }



}

