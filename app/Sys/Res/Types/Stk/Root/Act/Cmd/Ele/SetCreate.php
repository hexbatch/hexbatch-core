<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Annotations\ApiEventMarker;
use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\Sets\Params\CreateSetParamData;
use App\Enums\Sys\TypeOfAction;
use App\Models\Element;
use App\Models\ElementSet;
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

#[HexbatchTitle( title: "New set")]
#[HexbatchBlurb( blurb: "Creates a new set from a given element")]
#[HexbatchDescription( description: "Any element can become a set without loosing any of its element functionality.
## Sets are the bedrock of the library
 * element_uuid: required to create a set. An element can make one set
 * parent_set_uuid: A set can optionally have a parent set.  Parents cannot be changed later. Children can be parents.
 * bool has_events: A set can choose to turn off events fired when an element enters or leaves it. Cannot be changed later.
 *
 *
 * The owner of any set is the owner of its element, but elements can have their ownership changed
 * Sets can set up action hooks in the element type of do actions when its content changes, or filter what can enter


 \" ' > <
")]
#[ApiEventMarker( Evt\Type\SetCreating::class)] //pre
#[ApiEventMarker( Evt\Server\SetCreated::class)] //post d
#[ApiEventMarker( Evt\Set\SetChildCreated::class)] //post d
class SetCreate extends Act\Cmd\St implements ICommandCallable
{
    const UUID = '06c6d184-1230-4bd1-9ee4-80657a9e3620';
    const ACTION_NAME = TypeOfAction::CMD_SET_CREATE;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\St::class
    ];

    const EVENT_CLASSES = [
        Evt\Type\SetCreating::class,
        Evt\Server\SetCreated::class,
        Evt\Set\SetChildCreated::class
    ];



    #[ApiParamMarker( param_class: CreateSetParamData::class)]
    public function __construct(
        protected Element        $defining_element,
        protected bool           $has_events,
        protected bool           $is_system,
        protected ?UserNamespace $calling_namespace,
        protected ?string        $use_ref = null,
        protected ?ElementSet    $parent_set = null,
        protected bool           $do_permission_check = true,
        protected string         $child_key_to_use            = 'elements',


    )
    {

    }


    protected  function toArray() :array {
        return [
            'defining_element'=> $this->defining_element->toArray(),
            'parent_set'=> $this->parent_set->toArray(),
            'is_system'=> $this->is_system,
            'do_permission_check'=> $this->do_permission_check,
            'use_ref'=> $this->use_ref,
            'has_events'=> $this->has_events,
            'calling_namespace'=> $this->calling_namespace,
            'child_key_to_use'=> $this->child_key_to_use,
        ];
    }
    protected static function fromArray(array $args) : static{
        $is_system = (bool)$args['is_system'];
        $has_events = (bool)$args['has_events'];
        $do_permission_check = (bool)$args['do_permission_check'];
        $use_ref = $args['use_ref']??null;
        $parent_set = static::getSetFromArray('parent_set',$args,false);
        $defining_element = static::getElementFromArray('defining_element',$args);
        $calling_namespace = static::getNamespaceFromArray('calling_namespace',$args,false );
        $child_key_to_use = $args['child_key_to_use'];
        return new static(defining_element: $defining_element, has_events: $has_events, is_system: $is_system,
            calling_namespace: $calling_namespace, use_ref: $use_ref,parent_set: $parent_set,do_permission_check: $do_permission_check,child_key_to_use: $child_key_to_use);
    }





    const SET_KEY_IN_ARGS = 'set';

    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $created_set = null;
        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        if ($b_approved) {
            $created_set = $work->doCreateSet();
        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,
            data: [static::CHILD_DECISION_KEY =>$b_approved,static::SET_KEY_IN_ARGS=>$created_set,$work->child_key_to_use => $created_set]);
    }

    /**
     * @throws \Throwable
     */
    public function doCreateSet(bool $b_do_refresh = true ) : ElementSet {

        if ($this->do_permission_check) {
            static::checkIfGivenIsAdmin(given: $this->calling_namespace,target: $this->defining_element->element_namespace);
        }

        $set = new ElementSet();
        if ($this->use_ref) {
            $set->ref_uuid = $this->use_ref;
        }

        $set->parent_set_element_id = $this->defining_element->id;
        $set->has_events = $this->has_events;
        $set->is_system = $this->is_system;
        $set->save();

        if ($b_do_refresh) {
            $set = ElementSet::getThisSet(id: $set->id);
            $set->loadMissing('defining_element');
            $set->loadMissing('defining_type');
            $set->loadMissing('defining_element.element_parent_type');
        }

        return $set;
    }


    /**
     * @throws \Throwable
     */
    public static function createSetTree(
         Element        $defining_element,
         bool           $has_events,
         bool           $is_system,
         UserNamespace  $calling_namespace,
         ?string        $use_ref = null,
         ?ElementSet    $parent_set = null,
         bool           $do_permission_check = true,
         string         $child_key_to_use            = 'elements',
         ?IThangBuilder $builder = null
    ) : ElementSet|Thang|IThangBuilder
    {

        $defining_element->loadMissing('element_parent_type');
        $defining_element->loadMissing('element_namespace');

        if ($do_permission_check) {
            static::checkIfGivenIsAdmin(given: $calling_namespace,target: $defining_element->element_namespace);
            //element owner can create a set, permission of type chain optionally given
        }


        $node = new static(
            defining_element: $defining_element,
            has_events: $has_events,
            is_system: $is_system,
            calling_namespace: $calling_namespace,
            use_ref: $use_ref,
            do_permission_check: $do_permission_check,
            child_key_to_use: $child_key_to_use
        );

        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $builder?: $builder = ThangBuilder::createBuilder();
        $builder->setNamespace($calling_namespace);

        $builder->tree(
            command_class: Evt\Set\SetChildCreated::class,
            command_args: new Evt\Set\SetChildCreated(
                parent_set: $parent_set
            )->toArray(),
            command_tags: [Evt\Set\SetChildCreated::class]
        );

        $builder->leaf(
            command_class: Evt\Server\SetCreated::class,
            command_args: new Evt\Server\SetCreated()->toArray(),
            command_tags: [Evt\Server\SetCreated::class]
        );


        $builder->tree(
            command_class: static::class,
            command_args: $node->toArray(),
            command_tags: [static::class],
        );

        if ($is_system)
        {

            Evt\Type\SetCreating::callEventTree(
                defining_element: $defining_element,parent_set: $parent_set, builder: $builder);
        }



        if ($ret_builder) {
            return $builder;
        }

        return  $builder->execute()->getThang();
    }

}

