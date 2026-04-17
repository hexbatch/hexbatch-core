<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Annotations\ApiEventMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Models\Element;
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
#[ApiEventMarker( Evt\Type\ElementOwnerChange::class)] //pre
#[ApiEventMarker( Evt\Type\ElementRecieved::class)] //post
class ElementOwnerChange extends Act\Cmd\Ele implements ICommandCallable
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


    public function __construct(
        protected UserNamespace             $owner_namespace,
        protected bool                      $is_system,
        protected UserNamespace             $calling_namespace,

        /** @var Collection<Element>        $given_elements */
        protected Collection                $given_elements,


    )
    {

    }



    protected  function toArray() :array {
        return [
            'is_system'=> $this->is_system,
            'owner_namespace'=> $this->owner_namespace,
            'calling_namespace'=> $this->calling_namespace,
            'given_elements'=> $this->given_elements,
        ];
    }
    protected static function fromArray(array $args) : static{
        $is_system = (bool)$args['is_system'];
        $given_elements = static::getElementCollectionFromArray('given_elements',$args);
        $owner_namespace = static::getNamespaceFromArray('owner_namespace',$args);
        $calling_namespace = static::getNamespaceFromArray('calling_namespace',$args);
        return new static(
            owner_namespace: $owner_namespace, is_system: $is_system,
            calling_namespace: $calling_namespace,given_elements: $given_elements);
    }

    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        if ($b_approved) {
            $moved_elements = $work->moveElements();
        } else {
            $moved_elements = new Collection();
        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,
            data: [static::CHILD_DECISION_KEY =>$b_approved,'elements'=>$moved_elements->toArray()]);
    }


    /** @return Collection<Element> */
    protected function moveElements()
    : Collection
    {
        if ($this->is_system) {
            static::checkPermissions(given_elements: $this->given_elements, calling_namespace: $this->calling_namespace);
        }
        return $this->given_elements;

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
            static::checkIfGivenIsOwner(given: $calling_namespace,target: $a_ns);
        }
    }


    /**
     * @throws \Throwable
     */
    public static function changeElementOwnerTree(
         UserNamespace             $owner_namespace,
         bool                      $is_system,
         UserNamespace             $calling_namespace,

        /** @var Collection<Element>        $given_elements */
         Collection                $given_elements,
         ?IThangBuilder $builder = null
    ) : ElementType|Thang|IThangBuilder
    {

        if (!$is_system) {
            static::checkPermissions(given_elements: $given_elements, calling_namespace: $calling_namespace);
        }

        $node = new static(
            owner_namespace: $owner_namespace,
            is_system: $is_system,
            calling_namespace: $calling_namespace,
            given_elements: $given_elements
        );

        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $builder?: $builder = ThangBuilder::createBuilder();
        $builder->setNamespace($calling_namespace);

        Evt\Type\ElementRecieved::callRecievedTree(
            given_elements: $given_elements,recipient_namespace: $owner_namespace,
            number_of_elements: $given_elements->count(),builder: $builder);

        $builder->tree(
            command_class: static::class,
            command_args: $node->toArray(),
            command_tags: [static::class],
        );

        if ($is_system)
        {

            Evt\Type\ElementOwnerChange::callOwnerTree(
                given_elements: $given_elements,recipient_namespace: $owner_namespace,
                number_of_elements: $given_elements->count(),builder: $builder);
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

