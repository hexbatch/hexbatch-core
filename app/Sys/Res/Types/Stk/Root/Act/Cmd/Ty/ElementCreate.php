<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Annotations\ApiEventMarker;
use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\Elements\Params\CreateElementParamData;

use App\Enums\Sys\TypeOfAction;
use App\Exceptions\HexbatchNothingDoneException;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\Element;
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
#[ApiEventMarker( Evt\Type\ElementOwnerChange::class)] //pre
#[ApiEventMarker( Evt\Type\ElementCreation::class)] //pre
#[ApiEventMarker( Evt\Type\ElementRecieved::class)] //post

class ElementCreate extends Act\Cmd\Ele implements ICommandCallable
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
    ];



    #[ApiParamMarker( param_class: CreateElementParamData::class)]
    public function __construct(
        protected ElementType               $element_type,
        protected Phase                     $phase,
        protected int                       $number_to_create,
        protected UserNamespace             $owner_namespace,
        protected bool                      $is_system,
        protected UserNamespace             $calling_namespace,
        protected array                     $preassinged_uuids = []


    )
    {

    }

    protected  function toArray() :array {
        return [
            'element_type'=> $this->element_type->toArray(),
            'phase'=> $this->phase->toArray(),
            'number_to_create'=> $this->number_to_create,
            'is_system'=> $this->is_system,
            'owner_namespace'=> $this->owner_namespace,
            'calling_namespace'=> $this->calling_namespace,
            'preassinged_uuids'=> $this->preassinged_uuids,
        ];
    }
    protected static function fromArray(array $args) : static{
        $is_system = (bool)$args['is_system'];
        $preassinged_uuids = $args['preassinged_uuids'];
        $number_to_create = (int)$args['number_to_create'];
        $phase = static::getPhaseFromArray('phase',$args);
        $element_type = static::getTypeFromArray('element_type',$args);
        $owner_namespace = static::getNamespaceFromArray('owner_namespace',$args);
        $calling_namespace = static::getNamespaceFromArray('calling_namespace',$args);
        return new static(element_type: $element_type, phase: $phase, number_to_create: $number_to_create,
             owner_namespace: $owner_namespace, is_system: $is_system,
            calling_namespace: $calling_namespace,preassinged_uuids: $preassinged_uuids);
    }

    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        if ($b_approved) {
            $created_elements = $work->doCreateElement();
        } else {
            $created_elements = [];
        }

        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,
            data: [static::CHILD_DECISION_KEY =>$b_approved,'elements'=>$created_elements]);
    }

    /**
     * @throws \Throwable
     */
    protected function doCreateElement() : array {

        if ($this->is_system) {
            static::checkIfGivenIsAdmin(given: $this->calling_namespace,target: $this->element_type->owner_namespace);
        }

        $elements = [];

        if (!$this->element_type->isPublished()) {
            throw new HexbatchNotPossibleException(__("msg.type_must_be_published_before_making_elements",
                ['ref' => $this->element_type->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::TYPE_NEEDS_PUBLISHING);
        }


        if ($this->number_to_create <= 0) {
            throw new HexbatchNothingDoneException(__("msg.type_given_zero_elements_to_make",
                ['ref' => $this->element_type->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::TYPE_GIVEN_ZERO_TO_MAKE);
        }

        try {
            $uuid_index = 0;

            DB::beginTransaction();

            for ($set_index = 0; $set_index < $this->number_to_create; $set_index++) {
                $elements[] = $this->makeElement(loop_number: $uuid_index++);
            } //end non set creation


            $this->saveCollectionKeys();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $elements;
    }

    private function makeElement(int $loop_number) : Element
    {

        $phase_id = $this->phase->id;
        $namespace_owner_id = $this->owner_namespace->id;
        $type_id = $this->element_type->id;

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
        return $ele;
    }


    /**
     * @throws \Throwable
     */
    public static function createElementTree(
         ElementType               $element_type,
         Phase                     $phase,
         int                       $number_to_create,
         UserNamespace             $owner_namespace,
         bool                      $is_system,
         UserNamespace             $calling_namespace,
         array                     $preassinged_uuids = [],
        ?IThangBuilder $builder = null
    ) : ElementType|Thang|IThangBuilder
    {

        if (!$is_system) {
            static::checkIfGivenIsAdmin(given: $calling_namespace,target: $element_type->owner_namespace);
        }

        $node = new static(
            element_type: $element_type,
            phase: $phase,
            number_to_create: $number_to_create,
            owner_namespace: $owner_namespace,
            is_system: $is_system,
            calling_namespace: $calling_namespace,
            preassinged_uuids: $preassinged_uuids
        );

        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $builder?: $builder = ThangBuilder::createBuilder();
        $builder->setNamespace($calling_namespace);

        Evt\Type\ElementRecieved::callRecievedTree(
            given_elements: new Collection,recipient_namespace: $owner_namespace,
            number_of_elements: $number_to_create,builder: $builder);

        $builder->tree(
            command_class: static::class,
            command_args: $node->toArray(),
            command_tags: [static::class],
        );

        if ($is_system)
        {

            Evt\Type\ElementOwnerChange::callOwnerTree(
                given_elements: new Collection,recipient_namespace: $owner_namespace,
                number_of_elements: $number_to_create,builder: $builder);

            Evt\Type\ElementCreation::callOwnerTree(element_type: $element_type,
                given_elements: new Collection,recipient_namespace: $owner_namespace,
                number_of_elements: $number_to_create,builder: $builder);
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

