<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\Attributes\Params\AttributeParamData;
use App\Enums\Sys\TypeOfAction;
use App\Enums\Types\TypeOfApproval;
use App\Enums\Types\TypeOfLifecycle;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\Attribute;
use App\Models\LocationBound;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Data\Params\CommandParams;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Facades\Log;

#[HexbatchTitle( title: "Edit an attribute")]
#[HexbatchBlurb( blurb: "Change an existing attribute, no events are created. Reviews by others can be done when publishing")]
#[HexbatchDescription( description:'
# Editing an attribute.

The only time an attribute is editable is in design mode.

see  [DesignAttributeCreate](DesignAttributeCreate.php) for the argument list

Extra argument here is

* design_attribute_uuid : identify the attribute to be edited with the uuid

')]
#[ApiParamMarker( param_class: AttributeParamData::class)]
class DesignAttributeEdit extends Act\Cmd\Ds implements ICommandCallable
{
    const UUID = 'b5dc244c-d966-48fd-9c42-ed53cceb827f';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_ATTRIBUTE_EDIT;

    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

    const EVENT_CLASSES = [
        Evt\Type\DesignParentAdding::class
    ];

    #[ApiParamMarker( param_class: AttributeParamData::class)]
    public function __construct(
        protected AttributeParamData     $params,
        protected Attribute $given_attribute  ,
        protected UserNamespace      $calling_namespace,

    )
    {

    }

    protected  function toArray() :array {
        return [
            'params'=>$this->params->toArray(),
            'given_attribute'=>$this->given_attribute,
            'calling_namespace'=>$this->calling_namespace,
        ];
    }
    protected static function fromArray(array $args) : static{
        $params = AttributeParamData::from($args['params']);
        $calling_namespace = static::getNamespaceFromArray('calling_namespace',$args);
        $given_attribute = static::getAttributeFromArray('given_attribute',$args);
        return new static(params: $params,given_attribute: $given_attribute,calling_namespace: $calling_namespace);
    }



    protected function editAttribute()
    : Attribute
    {

        static::checkIfGivenIsAdmin(given: $this->calling_namespace,target: $this->given_attribute->type_owner->owner_namespace);

        if ($this->given_attribute->lifecycle === TypeOfLifecycle::PUBLISHED) {

            throw new HexbatchNotPossibleException(__('msg.design_cannot_add_attribute_to_published',['ref'=>$this->given_attribute->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::TYPE_SCHEMA_ISSUE);
        }



        if ($this->params->parent_ref_uuid) {
            $parent = Attribute::getThisAttribute(uuid: $this->params->parent_ref_uuid);
            if ($parent->is_final_attribute) {
                throw new HexbatchNotPossibleException(__('msg.attribute_parent_is_final',['ref'=>$parent->getName()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
            }

            if ($this->given_attribute->parent_attribute_id !== $parent->id)
            {
                $this->given_attribute->parent_attribute_id = $parent->id;
            }
        }

        if ($this->params->unset_parent) {
            $this->given_attribute->parent_attribute_id = null;
        }

        if ($this->params->location_uuid) {
            $shape_id = LocationBound::getThisLocation(uuid: $this->params->location_uuid)->id;
            $this->given_attribute->attribute_shape_id = $shape_id;
        }

        if ($this->params->attribute_name) {
            $this->given_attribute->setAttributeName($this->params->attribute_name);
        }

        if( $this->params->design_ref_uuid) {
            $design_attribute = Attribute::getThisAttribute(uuid: $this->params->design_ref_uuid);
            if (!($this->given_attribute->is_system || $design_attribute->isPublicDomain()) ) {
                static::checkIfGivenIsMember(given: $this->calling_namespace,target: $design_attribute->type_owner->owner_namespace );
            }
            $this->given_attribute->design_attribute_id = $design_attribute->id ;
        }

        if ($this->params->access_policy) {
            $this->given_attribute->access_policy = $this->params->access_policy ;
        }

        if ($this->params->value_policy) {
            $this->given_attribute->value_policy = $this->params->value_policy ;
        }

        if ($this->params->read_json_path) {
            $this->given_attribute->read_json_path = $this->params->read_json_path ;
        }

        if ($this->params->validate_json_path) {
            $this->given_attribute->validate_json_path = $this->params->validate_json_path ;
        }

        if (!empty($this->params->default_value)) {
            $this->given_attribute->setDefaultValue($this->params->default_value);
        }

        if ($this->params->is_final_attribute !== null ) {
            $this->given_attribute->is_final_attribute = $this->params->is_final_attribute ;
        }


        if ($this->params->is_abstract !== null ) {
            $this->given_attribute->is_abstract = $this->params->is_abstract ;
        }


        if ($this->given_attribute->isDirty()) {
            $this->given_attribute->attribute_approval = TypeOfApproval::PENDING_DESIGN_APPROVAL;
        }

        $this->given_attribute->save();
        $this->given_attribute->refresh();
        return $this->given_attribute;
    }


    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $work = static::fromArray($command_args);
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        if ($work->given_attribute) {
            if ($b_approved) {
                $work->given_attribute->attribute_approval = TypeOfApproval::DESIGN_APPROVED;
            } else {
                $work->given_attribute->attribute_approval = TypeOfApproval::DESIGN_DENIED;
            }
            $work->given_attribute->save();

        }

        Log::debug("Called design attribute edit node",['args'=>$command_args,'children'=>$children_args]);
        return new CallableReturnStub(status: $b_approved? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL,
            data: ['work'=>$work->toArray(),static::CHILD_DECISION_KEY=>$b_approved]);
    }

    /**
     * @throws \Throwable
     */
    public static function makeEditAttributeTree(
         AttributeParamData     $params,
         Attribute              $given_attribute  ,
        UserNamespace          $calling_namespace,
        ?IThangBuilder         $builder = null
    ) : Thang|IThangBuilder|null
    {
        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $me = new static(params: $params,given_attribute: $given_attribute, calling_namespace: $calling_namespace);

        $attribute = $me->editAttribute();

        $builder?: $builder = ThangBuilder::createBuilder();


        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>[static::class],
            'command_args' => $me->toArray()
        ]);
        $builder->tree($my_command);
        if ($attribute->attribute_parent) {
            Evt\Type\AttributePending::callParentTree(ancestor_attribute: $attribute->attribute_parent, given_attribute: $attribute, builder: $builder);
        }


        if ($ret_builder) {
            return $builder;
        }

        return  $builder->execute()->getThang();

    }
}

