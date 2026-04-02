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
use App\Models\ActionDatum;
use App\Models\Attribute;
use App\Models\LocationBound;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
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
        Evt\Server\DesignParentAdding::class
    ];

    #[ApiParamMarker( param_class: AttributeParamData::class)]
    public function __construct(
        protected AttributeParamData       $params,

        protected ?bool                    $is_async = null,
        protected bool                     $is_system = false,
        protected bool                     $send_event = true,
        protected ?ActionDatum             $action_data = null,
        protected ?ActionDatum             $parent_action_data = null,
        protected ?UserNamespace           $owner_namespace = null,
        protected bool                     $b_type_init = false,
        protected array                    $tags = []
    )
    {

        parent::__construct(action_data: $this->action_data, parent_action_data: $this->parent_action_data,owner_namespace: $this->owner_namespace,
            b_type_init: $this->b_type_init, is_system: $this->is_system, send_event: $this->send_event,is_async: $this->is_async,tags: $this->tags);
    }



    protected static function editAttribute(AttributeParamData $params,Attribute $given_attribute,UserNamespace $namespace)
    : Attribute
    {

        static::checkIfGivenIsAdmin(given: $namespace,target: $given_attribute->type_owner->owner_namespace);

        if ($given_attribute->lifecycle === TypeOfLifecycle::PUBLISHED) {

            throw new HexbatchNotPossibleException(__('msg.design_cannot_add_attribute_to_published',['ref'=>$given_attribute->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::TYPE_SCHEMA_ISSUE);
        }



        if ($params->parent_ref_uuid) {
            $parent = Attribute::getThisAttribute(uuid: $params->parent_ref_uuid);
            if ($parent->is_final_attribute) {
                throw new HexbatchNotPossibleException(__('msg.attribute_parent_is_final',['ref'=>$parent->getName()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
            }

            if ($given_attribute->parent_attribute_id !== $parent->id)
            {
                $given_attribute->parent_attribute_id = $parent->id;
            }
        }

        if ($params->unset_parent) {
            $given_attribute->parent_attribute_id = null;
        }

        if ($params->location_uuid) {
            $shape_id = LocationBound::getThisLocation(uuid: $params->location_uuid)->id;
            $given_attribute->attribute_shape_id = $shape_id;
        }

        if ($params->attribute_name) {
            $given_attribute->setAttributeName($params->attribute_name);
        }

        if( $params->design_ref_uuid) {
            $design_attribute = Attribute::getThisAttribute(uuid: $params->design_ref_uuid);
            if (!($given_attribute->is_system || $design_attribute->isPublicDomain()) ) {
                static::checkIfGivenIsMember(given: $namespace,target: $design_attribute->type_owner->owner_namespace );
            }
            $given_attribute->design_attribute_id = $design_attribute->id ;
        }

        if ($params->access_policy) {
            $given_attribute->access_policy = $params->access_policy ;
        }

        if ($params->value_policy) {
            $given_attribute->value_policy = $params->value_policy ;
        }

        if ($params->read_json_path) {
            $given_attribute->read_json_path = $params->read_json_path ;
        }

        if ($params->validate_json_path) {
            $given_attribute->validate_json_path = $params->validate_json_path ;
        }

        if (!empty($params->default_value)) {
            $given_attribute->setDefaultValue($params->default_value);
        }

        if ($params->is_final_attribute !== null ) {
            $given_attribute->is_final_attribute = $params->is_final_attribute ;
        }


        if ($params->is_abstract !== null ) {
            $given_attribute->is_abstract = $params->is_abstract ;
        }

        if ($given_attribute->isDirty()) {
            $given_attribute->attribute_approval = TypeOfApproval::PENDING_DESIGN_APPROVAL;
        }

        $given_attribute->save();
        $given_attribute->refresh();
        return $given_attribute;
    }

    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $params = AttributeParamData::validateAndCreate($command_args['attribute_params']);
        $attribute = $command_args['attribute']??null;
        $namespace = $command_args['namespace'];
        $edited_attribute = static::editAttribute(params: $params, given_attribute: $attribute, namespace: $namespace);
        Log::debug("Called design attribute edit node",['args'=>$command_args,'attribute'=>$edited_attribute]);
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $edited_attribute->toArray());
    }
}

