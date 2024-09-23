<?php

namespace App\Helpers\Attributes;

use App\Enums\Attributes\AttributeAccessType;
use App\Enums\Attributes\AttributeServerAccessType;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\Attribute;
use App\Models\ElementType;
use App\Rules\AttributeNameReq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AttributeGathering
{
    public ?ElementType $parent_type = null;

    public ?Attribute $current_attribute = null;
    public ?Attribute $parent_attribute = null;



    public ?int $applied_rule_bundle_id = null;
    public ?bool $is_retired = null;
    public ?bool $is_final_parent = null;
    public ?bool $is_using_ancestor_bundle = null;
    public ?bool $is_nullable = null;
    public ?bool $is_static = null;
    public ?bool $is_final = null;
    public ?bool $is_lazy = null;
    public ?string $value_json_path = null;
    public ?array $attribute_value = null;
    public ?string $attribute_name  = null;
    public ?AttributeServerAccessType $server_access_type = null;
    public ?AttributeAccessType $attribute_access_type = null;


    public function __construct(Request $request,ElementType $parent_type,?Attribute $current_attribute )
    {
        $this->parent_type = $parent_type;
        $this->current_attribute = $current_attribute;
        if ($parent_stuff = $request->request->get('parent_attribute')) {
            $this->parent_attribute = (new Attribute())->resolveRouteBinding($parent_stuff);
        }

        if ($current_attribute && ($parent_type->ref_uuid !== ($current_attribute->type_owner?->ref_uuid??null) ) ) {

            throw new HexbatchPermissionException(__("msg.attribute_owner_does_not_match_type_given",['ref'=>$current_attribute->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ATTRIBUTE_CANNOT_EDIT);
        }

        /*
         * given a parent, see if its type is readable
         */

        $user = Utilities::getTypeCastedAuthUser();

        if ($current_attribute && !$current_attribute->type_owner->canUserEdit($user)) {
            throw new HexbatchPermissionException(__("msg.attribute_cannot_be_edited_due_to_pivs",['ref'=>$current_attribute->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ATTRIBUTE_CANNOT_EDIT);

        }

        if ($this->parent_attribute) {
            if ($current_attribute ) {
                throw new HexbatchPermissionException(__("msg.attribute_parent_cannnot_change",['ref'=>$this->parent_attribute->getName()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                    RefCodes::ATTRIBUTE_CANNOT_BE_USED_AS_PARENT);
            }
            if( !$this->parent_attribute->type_owner->canUserEdit($user)) {
                throw new HexbatchPermissionException(__("msg.attribute_cannot_be_used_at_parent_permissions",['ref'=>$this->parent_attribute->getName()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                    RefCodes::ATTRIBUTE_CANNOT_BE_USED_AS_PARENT);
            }
        }

        //given a parent, see if its retired or is_final_parent
        if ($this->parent_attribute->is_retired || $this->parent_attribute->is_final_parent) {
            throw new HexbatchPermissionException(__("msg.attribute_cannot_be_used_at_parent_final",['ref'=>$this->parent_attribute->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ATTRIBUTE_CANNOT_BE_USED_AS_PARENT);
        }


        //fill in the local values
        if ( $request->request->has('is_using_ancestor_bundle')) {
            $this->is_using_ancestor_bundle = $request->request->getBoolean('is_using_ancestor_bundle');
        }
        if ($this->is_using_ancestor_bundle && $this->parent_attribute) {
            $this->applied_rule_bundle_id = $this->parent_attribute->applied_rule_bundle_id;
        }

        if ( $request->request->has('is_retired')) {
            $this->is_retired = $request->request->getBoolean('is_retired');
        }

        if ( $request->request->has('is_final_parent')) {
            $this->is_final_parent = $request->request->getBoolean('is_final_parent');
        }

        if ( $request->request->has('is_nullable')) {
            $this->is_nullable = $request->request->getBoolean('is_nullable');
        }

        if ( $request->request->has('is_static')) {
            $this->is_static = $request->request->getBoolean('is_static');
        }

        if ( $request->request->has('is_final')) {
            $this->is_final = $request->request->getBoolean('is_final');
        }

        if ( $request->request->has('is_lazy')) {
            $this->is_lazy = $request->request->getBoolean('is_lazy');
        }

        if ( $request->request->has('value_json_path')) {
            $this->value_json_path = $request->request->getString('value_json_path');
        }


        if ( $request->request->has('attribute_value')) {
            $pre_value = $request->get('attribute_value');
            if ( is_array($this->attribute_value) ) {
                $this->attribute_value = $pre_value;
            } else {
                $this->attribute_value = [$pre_value];
            }
        }



        if ( $request->request->has('attribute_name')) {
            $maybe_name = $request->request->getString('attribute_name');
            if ($maybe_name) {
                $this->validateName($maybe_name);
                $this->attribute_name = $maybe_name;
            }


        } else {
            if (!$this->current_attribute) {
                throw new HexbatchNotPossibleException(__('msg.attribute_schema_must_have_name'),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_BAD_NAME);
            }
        }

        if ( $request->request->has('server_access_type')) {
            $test_string = $request->request->getString('server_access_type');
            $this->server_access_type  = AttributeServerAccessType::tryFrom($test_string);
            if (!$this->server_access_type ) {
                throw new HexbatchNotPossibleException(__("msg.attribute_bad_server_access_type",['bad_type'=>$test_string]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
            }
        }

        if ( $request->request->has('attribute_access_type')) {
            $test_string = $request->request->getString('attribute_access_type');
            $this->attribute_access_type  = AttributeAccessType::tryFrom($test_string);
            if (!$this->attribute_access_type ) {
                throw new HexbatchNotPossibleException(__("msg.attribute_bad_access_type",['bad_type'=>$test_string]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
            }
        }

    }

    protected function validateName(string $name) {
        try {
            Validator::make(['attribute_name' => $name], [
                'attribute_name' => ['required', 'string', new AttributeNameReq($this->parent_type,$this->current_attribute)],
            ])->validate();
        } catch (ValidationException $v) {
            throw new HexbatchNotPossibleException($v->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ATTRIBUTE_BAD_NAME);
        }
    }



    public function assign() : Attribute {

        /**
         * @var Attribute $node
         */
        $node = $this->current_attribute;
        if (!$node) {
            $node = $this->current_attribute = new Attribute();
            $node->owner_element_type_id = $this->parent_type->id;
        }
        if ($this->parent_attribute) {
            $node->parent_attribute_id = $this->parent_attribute->id;
        }

        if ($node->isInUse()) {
            //some limited stuff
            if ($this->is_retired !== null ) { $node->is_retired = $this->is_retired; }
            if ($this->is_final_parent !== null ) { $node->is_final_parent = $this->is_final_parent; }
            if ($this->is_final !== null ) { $node->is_final = $this->is_final; }

        } else {
            if ($this->is_retired !== null ) { $node->is_retired = $this->is_retired; }
            if ($this->is_final_parent !== null ) { $node->is_final_parent = $this->is_final_parent; }
            if ($this->is_using_ancestor_bundle !== null ) { $node->is_using_ancestor_bundle = $this->is_using_ancestor_bundle; }
            if ($this->is_nullable !== null ) { $node->is_nullable = $this->is_nullable; }
            if ($this->is_static !== null ) { $node->is_static = $this->is_static; }
            if ($this->is_final !== null ) { $node->is_final = $this->is_final; }
            if ($this->is_lazy !== null ) { $node->is_lazy = $this->is_lazy; }
            if ($this->value_json_path !== null ) { $node->value_json_path = $this->value_json_path; }
            if ($this->attribute_value !== null ) { $node->attribute_value = $this->attribute_value; }
            if ($this->attribute_name !== null ) { $node->attribute_name = $this->attribute_name; }
            if ($this->server_access_type !== null ) { $node->server_access_type = $this->server_access_type; }
            if ($this->attribute_access_type !== null ) { $node->attribute_access_type = $this->attribute_access_type; }
        }
        $node->save();
        return $node;
    }

    public static function cloneAttribute(ElementType $new_parent_type,Attribute $current_attribute) : Attribute {
        $user = Utilities::getTypeCastedAuthUser();

        if ($new_parent_type->ref_uuid === $current_attribute->type_owner->ref_uuid) {

            throw new HexbatchPermissionException(__("msg.attribute_cannot_be_cloned_into_its_type",['ref'=>$current_attribute->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ATTRIBUTE_CANNOT_CLONE);
        }

        if (!$current_attribute->type_owner->canUserEdit($user)) {
            throw new HexbatchPermissionException(__("msg.attribute_cannot_be_cloned_due_to_pivs",['ref'=>$current_attribute->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ATTRIBUTE_CANNOT_CLONE);

        }

        if (!$new_parent_type->canUserEdit($user)) {
            throw new HexbatchPermissionException(__("msg.attribute_cannot_be_cloned_due_to_pivs",['ref'=>$current_attribute->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ATTRIBUTE_CANNOT_CLONE);
        }

        $cloned = $current_attribute->replicate(['created_at_ts','updated_at_ts','ref_uuid']);
        $cloned->owner_element_type_id = $new_parent_type->id;
        $cloned->save();
        $cloned->refresh();
        return $cloned;
    }

    public static function deleteAttribute(ElementType $element_type,Attribute $doomed_attribute) : void {
        if ($element_type->ref_uuid !== $doomed_attribute->type_owner->ref_uuid) {

            throw new HexbatchPermissionException(__("msg.attribute_owner_does_not_match_type_given",['ref'=>$doomed_attribute->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ATTRIBUTE_CANNOT_DELETE);
        }
        if ($doomed_attribute->isInUse()) {

            throw new HexbatchPermissionException(__("msg.attribute_cannot_be_deleted_if_in_use",['ref'=>$doomed_attribute->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ATTRIBUTE_CANNOT_DELETE);
        }
        $user = Utilities::getTypeCastedAuthUser();

        if (!$doomed_attribute->type_owner->canUserEdit($user)) {
            throw new HexbatchPermissionException(__("msg.attribute_cannot_be_deleted_priv",['ref'=>$doomed_attribute->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ATTRIBUTE_CANNOT_DELETE);
        }
        $doomed_attribute->delete();
    }

    public static function compareAttributeOwner(ElementType $parent_type,Attribute $current_attribute) : void  {
        if ($parent_type->ref_uuid !== ($current_attribute->type_owner?->ref_uuid??null) ) {

            throw new HexbatchPermissionException(__("msg.attribute_owner_does_not_match_type_given",['ref'=>$current_attribute->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ATTRIBUTE_CANNOT_EDIT);
        }
    }

    public static function attributeListCheck(ElementType $element_type) {


        $user = Utilities::getTypeCastedAuthUser();
        if ($element_type->canUserViewDetails($user)) {return;}

        throw new HexbatchPermissionException(__("msg.element_type_not_viewer",['ref'=>$element_type->getName()]),
            \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
            RefCodes::ELEMENT_TYPE_NOT_AUTHORIZED);

    }
}
