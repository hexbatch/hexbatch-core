<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\Attributes\Params\AttributeParamData;
use App\Enums\Attributes\TypeOfElementValuePolicy;
use App\Enums\Attributes\TypeOfServerAccess;
use App\Enums\Sys\TypeOfAction;
use App\Enums\Types\TypeOfApproval;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\Attribute;
use App\Models\ElementType;
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

#[HexbatchTitle( title: "Attribute create")]
#[HexbatchBlurb( blurb: "Attributes are made in design phase only, an optional parent is added after creation")]
#[HexbatchDescription( description: "
## Attributes can be set with the following properties

* uuid : when editing an existing attribute
* type_uuid: each attribute is defined as part of a type, but can be inherited by attributes elsewhere
* design_uuid: visually represents the attribute
* parent_uuid: this is set to pending, and the parent is notified to approve. If setting new parent, that one is asked to approve if not public domain
* location_uuid: attributes can have a shape
* is_final: cannot be a parent
* is_abstract: not usable by itself, must have a child
* access:  sets access across different servers
* value_policy: determines if the attribute can have multiple values for the same or all elements that use it
* read_json_path: if this is used, when the attribute value is always filtered by this
* validate_json_path: if this is used, when the attribute value is validated before being set
* default_value : if set, this is the default value before writing
* attribute_name: has to be unique in the namespace
* unset_parent : when editing an existing attribute and want to remove the parent

the type owner of the optional attribute parent will get a notice before creation

* [AttributePending](../../../Evt/Server/AttributePending.php)

This can decide to accept the new design using the parent or not, if they deny, the attribute is still created,
 but without the parent being approved, and it will be impossible to publish until this is changed
")]

class DesignAttributeCreate extends Act\Cmd\Ds implements ICommandCallable
{
    const UUID = '47661774-8acc-45fb-8c22-77663177e92c';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_ATTRIBUTE_CREATE;

    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\DesignParentAdding::class
    ];


    protected Attribute|null $parent_attribute = null;
    #[ApiParamMarker( param_class: AttributeParamData::class)]
    public function __construct(
        protected AttributeParamData     $params,
        protected bool              $is_system,
        protected ?string              $use_ref,
        protected UserNamespace      $calling_namespace,
        protected ElementType            $given_type

    )
    {

    }

    protected  function toArray() :array {
        return [
            'params'=>$this->params->toArray(),
            'use_ref'=>$this->use_ref,
            'is_system'=>$this->is_system,
            'calling_namespace'=>$this->calling_namespace,
            'given_type'=>$this->given_type,
        ];
    }
    protected static function fromArray(array $args) : static{
        $params = AttributeParamData::from($args['params']);
        $is_system = (bool)$args['is_system'];
        $use_ref = $args['use_ref'];
        $calling_namespace = static::getNamespaceFromArray('calling_namespace',$args);
        $given_type = static::getTypeFromArray('given_type',$args);
        return new static(params: $params,is_system: $is_system,use_ref: $use_ref,calling_namespace: $calling_namespace,given_type: $given_type);
    }




    protected  function doCreateAttribute()
    : Attribute
    {
        if (!$this->is_system) {
            static::checkIfGivenIsAdmin(given: $this->calling_namespace,target: $this->given_type->owner_namespace);
        }


        $given_attribute = new Attribute();
        $given_attribute->owner_element_type_id = $this->given_type->id;
        $given_attribute->attribute_approval = TypeOfApproval::PENDING_DESIGN_APPROVAL;
        $given_attribute->is_system = $this->is_system;
        $given_attribute->setAttributeName($this->params->attribute_name??null);

        if ($this->params->parent_ref_uuid) {
            $this->parent_attribute = $parent = Attribute::getThisAttribute(uuid: $this->params->parent_ref_uuid);
            if ($parent->is_final_attribute) {
                throw new HexbatchNotPossibleException(__('msg.attribute_parent_is_final',['ref'=>$parent->getName()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
            }

            $given_attribute->parent_attribute_id = $parent->id;
        }


        if ($this->params->location_uuid) {
            $shape_id = LocationBound::getThisLocation(uuid: $this->params->location_uuid)->id;
            $given_attribute->attribute_shape_id = $shape_id;
        }


        if( $this->params->design_ref_uuid) {
            $design_attribute = Attribute::getThisAttribute(uuid: $this->params->design_ref_uuid);
            if (!($given_attribute->is_system || $design_attribute->isPublicDomain()) ) {
                static::checkIfGivenIsMember(given: $this->calling_namespace,target: $design_attribute->type_owner->owner_namespace );
            }
            $given_attribute->design_attribute_id = $design_attribute->id ;
        }


        $given_attribute->access_policy = $this->params->access_policy?? TypeOfServerAccess::IS_PRIVATE;
        $given_attribute->value_policy = $this->params->value_policy?? TypeOfElementValuePolicy::STATIC;

        $given_attribute->read_json_path = $this->params->read_json_path??null ;
        $given_attribute->validate_json_path = $this->params->validate_json_path??null ;
        $given_attribute->is_final_attribute = $this->params->is_final_attribute??false ;
        $given_attribute->is_abstract = $this->params->is_abstract?? false  ;

        if (!empty($this->params->default_value)) {
            $given_attribute->setDefaultValue($this->params->default_value);
        }


        $given_attribute->save();
        return $given_attribute;
    }

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $attribute = null;
        $b_approved = true;
        if (count($children_args)) {
            $b_approved = $children_args[static::CHILD_DECISION_KEY]??false;
            /** @var Attribute $attribute */
            $attribute = $children_args['attribute'];
        }
        if ($attribute) {
            if ($b_approved) {
                $attribute->attribute_approval = TypeOfApproval::DESIGN_APPROVED;
            } else {
                $attribute->attribute_approval = TypeOfApproval::DESIGN_DENIED;
            }
            $attribute->save();

        }

        Log::debug("Called design attribute create node",['args'=>$command_args,'children'=>$children_args]);
        return new CallableReturnStub(status: $b_approved? TypeOfCmdStatus::CMD_SUCCESS: TypeOfCmdStatus::CMD_FAIL,
            data: ['attribute'=>$attribute,static::CHILD_DECISION_KEY=>$b_approved]);
    }

    /**
     * @throws \Throwable
     */
    public static function makeCreateAttributeTree(
         AttributeParamData     $params,
         bool                   $is_system,
         ?string                $use_ref,
         UserNamespace          $calling_namespace,
         ElementType            $given_type,
         ?IThangBuilder         $builder = null
    ) : Thang|IThangBuilder|null
    {
        $ret_builder = false;
        if ($builder) {
            $ret_builder = true;
        }

        $me = new static(params: $params,is_system: $is_system,
            use_ref: $use_ref,calling_namespace: $calling_namespace,given_type: $given_type);

        $attribute = $me->doCreateAttribute();



        $builder?: $builder = ThangBuilder::createBuilder();


        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>[static::class],
            'command_args' => $me->toArray()
        ]);
        $builder->tree($my_command);
        if (!$is_system && $params->parent_ref_uuid) {
            Evt\Server\AttributePending::callParentTree(ancestor_attribute: $me->parent_attribute, given_attribute: $attribute, builder: $builder);
        }


        if ($ret_builder) {
            return $builder;
        }

        return  $builder->execute()->getThang();

    }
}

