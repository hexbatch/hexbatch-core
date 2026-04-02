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
use App\Models\ActionDatum;
use App\Models\Attribute;
use App\Models\ElementType;
use App\Models\LocationBound;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
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


    #[ApiParamMarker( param_class: AttributeParamData::class)]
    protected static function createAttribute(AttributeParamData $params,ElementType $given_type,UserNamespace $namespace)
    : Attribute
    {
        //todo attributes are never admin, only types are
        static::checkIfGivenIsAdmin(given: $namespace,target: $given_type->owner_namespace);

        $given_attribute = new Attribute();
        $given_attribute->owner_element_type_id = $given_type->id;
        $given_attribute->attribute_approval = TypeOfApproval::PENDING_DESIGN_APPROVAL;

        $given_attribute->setAttributeName($params->attribute_name??null);

        if ($params->parent_ref_uuid) {
            $parent = Attribute::getThisAttribute(uuid: $params->parent_ref_uuid);
            if ($parent->is_final_attribute) {
                throw new HexbatchNotPossibleException(__('msg.attribute_parent_is_final',['ref'=>$parent->getName()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
            }

            $given_attribute->parent_attribute_id = $parent->id;
        }


        if ($params->location_uuid) {
            $shape_id = LocationBound::getThisLocation(uuid: $params->location_uuid)->id;
            $given_attribute->attribute_shape_id = $shape_id;
        }


        if( $params->design_ref_uuid) {
            $design_attribute = Attribute::getThisAttribute(uuid: $params->design_ref_uuid);
            if (!($given_attribute->is_system || $design_attribute->isPublicDomain()) ) {
                static::checkIfGivenIsMember(given: $namespace,target: $design_attribute->type_owner->owner_namespace );
            }
            $given_attribute->design_attribute_id = $design_attribute->id ;
        }


        $given_attribute->access_policy = $params->access_policy?? TypeOfServerAccess::IS_PRIVATE;
        $given_attribute->value_policy = $params->value_policy?? TypeOfElementValuePolicy::STATIC;

        $given_attribute->read_json_path = $params->read_json_path??null ;
        $given_attribute->validate_json_path = $params->validate_json_path??null ;
        $given_attribute->is_final_attribute = $params->is_final_attribute??false ;
        $given_attribute->is_abstract = $params->is_abstract?? false  ;

        if (!empty($params->default_value)) {
            $given_attribute->setDefaultValue($params->default_value);
        }


        $given_attribute->save();
        return $given_attribute;
    }

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $params = AttributeParamData::validateAndCreate($command_args['attribute_params']);
        $element_type = $command_args['element_type']??null;
        $namespace = $command_args['namespace'];
        $edited_attribute = static::createAttribute(params: $params, given_type: $element_type, namespace: $namespace);
        Log::debug("Called design attribute create node",['args'=>$command_args,'attribute'=>$edited_attribute]);
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $edited_attribute->toArray());
    }
}

