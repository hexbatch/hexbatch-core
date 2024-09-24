<?php

namespace App\Http\Controllers\API;


use App\Enums\Attributes\AttributePingType;
use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Helpers\Attributes\AttributeGathering;
use App\Helpers\Attributes\RuleGathering;
use App\Helpers\ElementTypes\TypeGathering;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;


use App\Http\Resources\AttributeCollection;
use App\Http\Resources\AttributeResource;
use App\Http\Resources\AttributeRuleCollection;
use App\Http\Resources\AttributeRuleResource;
use App\Http\Resources\ElementTypeCollection;
use App\Http\Resources\ElementTypeResource;
use App\Models\Attribute;
use App\Models\AttributeRule;
use App\Models\ElementType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TypeController extends Controller
{


    /**
     * @throws \Exception
     */
    public function create_type(Request $request): JsonResponse {
        try {
            DB::beginTransaction();
            $gathering = new TypeGathering($request);
            $element_type = $gathering->assign();
            $refreshed = ElementType::buildElementType(id: $element_type->id)?->first();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return response()->json(new ElementTypeResource($refreshed,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    /**
     * @param ElementType $element_type
     * @param Request $request
     * @return JsonResponse
     */
    public function edit_type(ElementType $element_type,Request $request): JsonResponse {
        $gathering = new TypeGathering($request,$element_type);
        $element_type = $gathering->assign();
        $refreshed = ElementType::buildElementType(id:$element_type->id)->first();
        return response()->json(new ElementTypeResource($refreshed,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function destroy_type(ElementType $element_type): JsonResponse {
        $user = Utilities::getTypeCastedAuthUser();
        if ($element_type->user_id !== $user->id) {
            throw new HexbatchPermissionException(__("msg.element_type_only_owner_can_delete",['ref'=>$element_type->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ELEMENT_TYPE_ONLY_OWNER_CAN_DELETE);
        }

        if ($element_type->isInUse()) {
            throw new HexbatchPermissionException(__("msg.element_type_only_delete_if_unused",['ref'=>$element_type->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ELEMENT_TYPE_CANNOT_DELETE);
        }

        $element_type->delete();
        return response()->json(new ElementTypeResource($element_type), \Symfony\Component\HttpFoundation\Response::HTTP_OK);

    }
    public function get_type(ElementType $element_type): JsonResponse {
        return response()->json(new ElementTypeResource($element_type,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function list_types(): JsonResponse {
        $user = Utilities::getTypeCastedAuthUser();
        $list = ElementType::buildElementType(user_id:$user->id);
        $ret = $list->cursorPaginate();
        return (new ElementTypeCollection($ret))
            ->response()->setStatusCode(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function type_ping(Request $request,ElementType $element_type,AttributePingType $attribute_ping_type): JsonResponse {
        AttributeGathering::attributeListCheck($element_type);
        $ret = TypeGathering::doPing($request,$element_type,$attribute_ping_type);
        return response()->json($ret, \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function attribute_ping(Request $request,ElementType $element_type,Attribute $attribute,AttributePingType $attribute_ping_type): JsonResponse {
        AttributeGathering::compareAttributeOwner($element_type,$attribute);
        AttributeGathering::attributeListCheck($element_type);
        $ret = AttributeGathering::doPing($request,$attribute,$attribute_ping_type);
        return response()->json($ret, \Symfony\Component\HttpFoundation\Response::HTTP_OK);

    }

    public function attribute_rule_ping(Request $request,ElementType $element_type,Attribute $attribute,AttributeRule $attribute_rule,AttributePingType $attribute_ping_type)
    : JsonResponse
    {
        AttributeGathering::compareAttributeOwner($element_type,$attribute);
        RuleGathering::checkRuleBelongsInAttribute($attribute,$attribute_rule);
        AttributeGathering::attributeListCheck($element_type);
        $ret = RuleGathering::doPing($request,$attribute_rule,$attribute_ping_type);
        return response()->json($ret, \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function new_attribute(Request $request,ElementType $element_type): JsonResponse {
        $attribute = (new AttributeGathering($request,$element_type,null) )->assign();
        $out = Attribute::buildAttribute(id:$attribute->id)->first();
        return response()->json(new AttributeResource($out,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function edit_attribute(Request $request,ElementType $element_type, Attribute $attribute): JsonResponse {
        $attribute = (new AttributeGathering($request,$element_type,$attribute) )->assign();
        $out = Attribute::buildAttribute(id:$attribute->id)->first();
        return response()->json(new AttributeResource($out,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function copy_attribute(ElementType $element_type, Attribute $source_attribute): JsonResponse {
        $cloned = AttributeGathering::cloneAttribute($element_type,$source_attribute);
        $out = Attribute::buildAttribute(id:$cloned->id)->first();
        return response()->json(new AttributeResource($out,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function delete_attribute(ElementType $element_type, Attribute $doomed_attribute): JsonResponse {
        AttributeGathering::deleteAttribute($element_type,$doomed_attribute);
        return response()->json(new AttributeResource($doomed_attribute,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function attribute_get(ElementType $element_type, Attribute $attribute,?int $levels = 2): JsonResponse {
        AttributeGathering::compareAttributeOwner($element_type,$attribute);
        return response()->json(new AttributeResource($attribute,null,$levels), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function attributes_list(ElementType $element_type,?string $filter = null): JsonResponse {
        Utilities::ignoreVar($filter);
        AttributeGathering::attributeListCheck($element_type);
        $laravel_list = Attribute::buildAttribute(element_type_id: $element_type->id);
        $ret = $laravel_list->cursorPaginate();
        return (new AttributeCollection($ret))
            ->response()->setStatusCode(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function attribute_list_rules(ElementType $element_type,Attribute $attribute,?string $filter = null): JsonResponse {
        Utilities::ignoreVar($filter);
        AttributeGathering::compareAttributeOwner($element_type,$attribute);
        AttributeGathering::attributeListCheck($element_type);
        $out = $attribute->rule_bundle?->rules_in_group??[];
        return response()->json(new AttributeRuleCollection($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function attribute_new_rule(Request $request,ElementType $element_type,Attribute $attribute): JsonResponse {
        AttributeGathering::compareAttributeOwner($element_type,$attribute);
        AttributeGathering::checkCurrentUserEditAttribute($attribute);
        $rule = (new RuleGathering($request,$element_type,$attribute))->assign();
        $out = AttributeRule::buildAttributeRule(id:$rule->id)->first();
        return response()->json(new AttributeRuleResource($out,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }



    public function attribute_edit_rule(Request $request,ElementType $element_type,Attribute $attribute,AttributeRule $attribute_rule): JsonResponse {
        AttributeGathering::compareAttributeOwner($element_type,$attribute);
        RuleGathering::checkRuleBelongsInAttribute($attribute,$attribute_rule);
        RuleGathering::checkRuleEditPermission($attribute,$attribute_rule);
        $rule = (new RuleGathering($request,$element_type,$attribute,$attribute_rule))->assign();
        $out = AttributeRule::buildAttributeRule(id:$rule->id)->first();
        return response()->json(new AttributeRuleResource($out,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function attribute_delete_rule(ElementType $element_type,Attribute $attribute,AttributeRule $attribute_rule): JsonResponse {
        AttributeGathering::compareAttributeOwner($element_type,$attribute);
        RuleGathering::checkRuleBelongsInAttribute($attribute,$attribute_rule);
        RuleGathering::deleteRule($attribute,$attribute_rule);
        return response()->json(new AttributeRuleResource($attribute_rule,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function attribute_clear_rules(ElementType $element_type,Attribute $attribute): JsonResponse {
        AttributeGathering::compareAttributeOwner($element_type,$attribute);
        AttributeGathering::checkCurrentUserEditAttribute($attribute);
        $old_rules = [];
        if ($attribute->rule_bundle?->creator_attribute?->ref_uuid === $attribute->ref_uuid) {
            $old_rules = $attribute->rule_bundle?->rules_in_group??[];
            $attribute->rule_bundle?->delete();
        }

        return response()->json(new AttributeRuleCollection($old_rules), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function attribute_get_rule(ElementType $element_type,Attribute $attribute,AttributeRule $attribute_rule,?string $levels = null): JsonResponse {
        AttributeGathering::compareAttributeOwner($element_type,$attribute);
        AttributeGathering::attributeListCheck($element_type);
        RuleGathering::checkRuleBelongsInAttribute($attribute,$attribute_rule);
        return response()->json(new AttributeRuleResource($attribute_rule,null,$levels), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

}
