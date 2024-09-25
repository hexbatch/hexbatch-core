<?php

namespace App\Helpers\Attributes;

use App\Enums\Attributes\AttributeAccessType;
use App\Enums\Attributes\AttributePingType;
use App\Enums\Attributes\AttributeServerAccessType;
use App\Enums\Bounds\LocationType;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Helpers\Bounds\LocationBoundGathering;
use App\Helpers\Bounds\TimeBoundGathering;
use App\Helpers\Utilities;
use App\Models\Attribute;
use App\Models\ElementType;
use App\Models\ElementTypeHorde;
use App\Models\LocationBound;
use App\Models\TimeBound;
use App\Rules\AttributeNameReq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
    public ?bool $is_const = null;
    public ?bool $is_final = null;
    public ?bool $is_per_set_value = null;
    public ?string $value_json_path = null;
    public ?array $attribute_value = null;
    public ?string $attribute_name  = null;
    public ?AttributeServerAccessType $server_access_type = null;
    public ?AttributeAccessType $attribute_access_type = null;
    public ?int $attribute_time_bound_id = null;
    public ?int $attribute_location_bound_id = null;


    public static function checkCurrentUserEditAttribute(?Attribute $current_attribute) {
        if (!$current_attribute) {return;}

        $user = Utilities::getTypeCastedAuthUser();

        if ( !$current_attribute->type_owner->canUserEdit($user)) {
            throw new HexbatchPermissionException(__("msg.attribute_cannot_be_edited_due_to_pivs",['ref'=>$current_attribute->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ATTRIBUTE_CANNOT_EDIT);

        }
    }
    public function __construct(Request $request,ElementType $parent_type,?Attribute $current_attribute )
    {
        try {
            DB::beginTransaction();
            $this->parent_type = $parent_type;
            $this->current_attribute = $current_attribute;
            if ($parent_stuff = $request->request->get('parent_attribute')) {
                $this->parent_attribute = (new Attribute())->resolveRouteBinding($parent_stuff);
            }

            if ($current_attribute && ($parent_type->ref_uuid !== ($current_attribute->type_owner?->ref_uuid ?? null))) {

                throw new HexbatchPermissionException(__("msg.attribute_owner_does_not_match_type_given", ['ref' => $current_attribute->getName()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                    RefCodes::ATTRIBUTE_CANNOT_EDIT);
            }

            /*
             * given a parent, see if its type is readable
             */

            $user = Utilities::getTypeCastedAuthUser();

            static::checkCurrentUserEditAttribute($current_attribute);

            if ($this->parent_attribute) {
                if ($current_attribute) {
                    throw new HexbatchPermissionException(__("msg.attribute_parent_cannnot_change", ['ref' => $this->parent_attribute->getName()]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                        RefCodes::ATTRIBUTE_CANNOT_BE_USED_AS_PARENT);
                }
                if (!$this->parent_attribute->type_owner->canUserEdit($user)) {
                    throw new HexbatchPermissionException(__("msg.attribute_cannot_be_used_at_parent_permissions", ['ref' => $this->parent_attribute->getName()]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                        RefCodes::ATTRIBUTE_CANNOT_BE_USED_AS_PARENT);
                }
            }

            //given a parent, see if its retired or is_final_parent
            if ($this->parent_attribute->is_retired || $this->parent_attribute->is_final_parent) {
                throw new HexbatchPermissionException(__("msg.attribute_cannot_be_used_at_parent_final", ['ref' => $this->parent_attribute->getName()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                    RefCodes::ATTRIBUTE_CANNOT_BE_USED_AS_PARENT);
            }


            //fill in the local values
            if ($request->request->has('is_using_ancestor_bundle')) {
                $this->is_using_ancestor_bundle = $request->request->getBoolean('is_using_ancestor_bundle');
            }
            if ($this->is_using_ancestor_bundle && $this->parent_attribute) {
                $this->applied_rule_bundle_id = $this->parent_attribute->applied_rule_bundle_id;
            }

            if ($request->request->has('is_retired')) {
                $this->is_retired = $request->request->getBoolean('is_retired');
            }

            if ($request->request->has('is_final_parent')) {
                $this->is_final_parent = $request->request->getBoolean('is_final_parent');
            }

            if ($request->request->has('is_nullable')) {
                $this->is_nullable = $request->request->getBoolean('is_nullable');
            }

            if ($request->request->has('is_const')) {
                $this->is_const = $request->request->getBoolean('is_const');
            }

            if ($request->request->has('is_final')) {
                $this->is_final = $request->request->getBoolean('is_final');
            }

            if ($request->request->has('is_per_set_value')) {
                $this->is_per_set_value = $request->request->getBoolean('is_per_set_value');
            }


            if ($request->request->has('value_json_path')) {
                $this->value_json_path = $request->request->getString('value_json_path');
                Utilities::testValidJsonPath($this->value_json_path);
            }


            if ($request->request->has('attribute_value')) {
                $pre_value = $request->get('attribute_value');
                if (is_array($this->attribute_value)) {
                    $this->attribute_value = $pre_value;
                } else {
                    $this->attribute_value = [$pre_value];
                }
            }


            if ($request->request->has('attribute_name')) {
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

            if ($request->request->has('server_access_type')) {
                $test_string = $request->request->getString('server_access_type');
                $this->server_access_type = AttributeServerAccessType::tryFrom($test_string);
                if (!$this->server_access_type) {
                    throw new HexbatchNotPossibleException(__("msg.attribute_bad_server_access_type", ['bad_type' => $test_string]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                }
            }

            if ($request->request->has('attribute_access_type')) {
                $test_string = $request->request->getString('attribute_access_type');
                $this->attribute_access_type = AttributeAccessType::tryFrom($test_string);
                if (!$this->attribute_access_type) {
                    throw new HexbatchNotPossibleException(__("msg.attribute_bad_access_type", ['bad_type' => $test_string]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                }
            }


            if ($request->request->has('attribute_time_bound')) {
                /**
                 * @var TimeBound|null $time_bound
                 */
                $time_bound = null;
                $hint_time_bound = $request->get('attribute_time_bound');
                if (is_string($hint_time_bound) && Utilities::is_uuid($hint_time_bound)) {
                    $time_bound = (new TimeBound())->resolveRouteBinding($hint_time_bound);
                } else if (is_array($hint_time_bound)) {
                    $time_bound = (new TimeBoundGathering($request->collect('attribute_time_bound')))->assign();
                }
                if ($time_bound) {
                    $this->attribute_time_bound_id = $time_bound->id;
                }
            }

            if ($request->request->has('attribute_location_bound')) {
                /**
                 * @var LocationBound|null $time_bound
                 */
                $loc_bound = null;
                $hint_location_bound = $request->get('attribute_location_bound');
                if (is_string($hint_location_bound) && Utilities::is_uuid($hint_location_bound)) {
                    $loc_bound = (new LocationBound())->resolveRouteBinding($hint_location_bound);
                } else if (is_array($hint_location_bound)) {
                    $loc_bound = (new LocationBoundGathering($request->collect('attribute_location_bound')))->assign();
                }
                if ($loc_bound) {
                    $this->attribute_location_bound_id = $loc_bound->id;
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
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


    /**
     * @throws \Exception
     */
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
            if ($this->is_const !== null ) { $node->is_const = $this->is_const; }
            if ($this->is_final !== null ) { $node->is_final = $this->is_final; }
            if ($this->is_per_set_value !== null ) { $node->is_per_set_value = $this->is_per_set_value; }
            if ($this->value_json_path !== null ) { $node->value_json_path = $this->value_json_path; }
            if ($this->attribute_value !== null ) { $node->attribute_value = $this->attribute_value; }
            if ($this->attribute_name !== null ) { $node->attribute_name = $this->attribute_name; }
            if ($this->server_access_type !== null ) { $node->server_access_type = $this->server_access_type; }
            if ($this->attribute_access_type !== null ) { $node->attribute_access_type = $this->attribute_access_type; }
            if ($this->attribute_location_bound_id !== null ) { $node->attribute_location_bound_id = $this->attribute_location_bound_id; }
            if ($this->attribute_time_bound_id !== null ) { $node->attribute_time_bound_id = $this->attribute_time_bound_id; }
        }


        try {
            DB::beginTransaction();
            $node->save();


            ElementTypeHorde::addAttribute($node,$this->parent_type); //will throw when finds fist issue, and then will not save changes
            ElementTypeHorde::checkAttributeConflicts($this->parent_type); //will throw when finds fist issue, and then will not save changes
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return $node;
    }

    /**
     * @throws \Exception
     */
    public static function cloneAttribute(ElementType $new_parent_type, Attribute $current_attribute) : Attribute {
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
        try {
            DB::beginTransaction();
            $cloned->save();
            $cloned->refresh();

            ElementTypeHorde::addAttribute($cloned,$new_parent_type); //will throw when finds fist issue, and then will not save changes
            ElementTypeHorde::checkAttributeConflicts($new_parent_type); //will throw when finds fist issue, and then will not save changes
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }


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


        try {
            DB::beginTransaction();
            $doomed_attribute->delete();
            ElementTypeHorde::checkAttributeConflicts($element_type); //will throw when finds fist issue, and then will not save changes
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public static function compareAttributeOwner(ElementType $parent_type,Attribute $current_attribute) : void  {
        //get the attribute owner type from the horde, this shows belonging
        $b_exists = ElementTypeHorde::where('horde_type_id',$parent_type->id)->where('horde_attribute_id',$current_attribute->id)->exists();
        if (!$b_exists ) {
            throw new HexbatchPermissionException(__("msg.attribute_owner_does_not_match_type_given",['ref'=>$current_attribute->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ATTRIBUTE_CANNOT_EDIT);
        }
    }



    public static function doPing(Request $request,Attribute $attribute,AttributePingType $attribute_ping_type) : array
    {
        $ret = [];

        $location_to_ping = $request->get('location_ping');
        $shape_to_ping = $request->get('shape_ping');
        $time_string = $request->get('time_string');

        $location_to_ping_json = '';
        if (!empty($location_to_ping)) {
            $location_to_ping_json = json_encode($location_to_ping);
        }

        $shape_to_ping_json = '';
        if (!empty($shape_to_ping)) {
            $shape_to_ping_json = json_encode($shape_to_ping);
        }


        if ($attribute_ping_type === AttributePingType::ALL || $attribute_ping_type === AttributePingType::ALL_TIME) {
            if ($attribute->attribute_time_bound) {
                $ret['time'] = $attribute->attribute_time_bound->ping($time_string);
            }
        }

        if ($attribute_ping_type === AttributePingType::ALL || $attribute_ping_type === AttributePingType::ALL_MAP) {
            if (empty($location_to_ping )) {
                throw new HexbatchNotPossibleException(__("msg.attribute_ping_missing_data"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_PING_DATA_MISSING);
            }
            if($attribute->attribute_location_bound && $attribute->attribute_location_bound->location_type === LocationType::MAP ) {
                $ret['map'] = $attribute->attribute_location_bound->ping($location_to_ping_json);
            }
        }

        if ($attribute_ping_type === AttributePingType::ALL || $attribute_ping_type === AttributePingType::ALL_SHAPE) {
            if (empty($location_to_ping )) {
                throw new HexbatchNotPossibleException(__("msg.attribute_ping_missing_data"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_PING_DATA_MISSING);
            }
            if($attribute->attribute_location_bound && $attribute->attribute_location_bound->location_type === LocationType::SHAPE ) {
                $ret['shape'] = $attribute->attribute_location_bound->ping($shape_to_ping_json);
            }
        }

        return $ret;
    }
}
