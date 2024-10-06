<?php

namespace App\Http\Controllers\API;


use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
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


class TypeController extends Controller
{


    /**
     * @throws \Exception
     */
    public function create_type(Request $request): JsonResponse {


        $element_type = ElementType::collectType($request->collect());
        $refreshed = ElementType::buildElementType(id: $element_type->id)?->first();
        return response()->json(new ElementTypeResource($refreshed,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    /**
     * @throws \Exception
     */
    public function edit_type(ElementType $element_type,Request $request): JsonResponse {
        $element_type->editType($request->collect());
        $refreshed = ElementType::buildElementType(id:$element_type->id)->first();
        return response()->json(new ElementTypeResource($refreshed,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function destroy_type(ElementType $element_type): JsonResponse {

        if ($element_type->isInUse()) {

            throw new HexbatchPermissionException(__("msg.type_only_delete_if_unused",['ref'=>$element_type->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ATTRIBUTE_CANNOT_DELETE);
        }
        //todo put the type delete into the thing
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_NOT_IMPLEMENTED);

    }
    public function get_type(ElementType $element_type,?int $levels = 3): JsonResponse {
        return response()->json(new ElementTypeResource($element_type,null,$levels), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function list_types(): JsonResponse {
        $user = Utilities::getTypeCastedAuthUser();
        $list = ElementType::buildElementType(owner_namespace_id:$user->id);
        $ret = $list->cursorPaginate();
        return (new ElementTypeCollection($ret))
            ->response()->setStatusCode(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function type_ping_map(Request $request,ElementType $element_type): JsonResponse {
        $ret = $element_type->type_map->ping($request->get('location'));
        return response()->json($ret, \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }
    public function type_ping_time(Request $request,ElementType $element_type): JsonResponse {
        $ret = $element_type->type_time->ping($request->get('time'));
        return response()->json($ret, \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function attribute_ping_shape(Request $request, ElementType $element_type, Attribute $attribute): JsonResponse {
        Utilities::ignoreVar($element_type); //checked in the middleware
        $ret = $attribute->attribute_shape_bound->ping($request->get('location'));
        return response()->json($ret, \Symfony\Component\HttpFoundation\Response::HTTP_OK);

    }


    /**
     * @throws \Exception
     */
    public function new_attribute(Request $request,ElementType $element_type): JsonResponse {
        $attribute = Attribute::collectAttribute(collect: $request->collect(),owner: $element_type);
        $out = Attribute::buildAttribute(id:$attribute->id)->first();
        return response()->json(new AttributeResource($out,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    /**
     * @throws \Exception
     */
    public function edit_attribute(Request $request,ElementType $element_type, Attribute $attribute): JsonResponse {
        $attribute = Attribute::collectAttribute(collect: $request->collect(), owner: $element_type, attribute: $attribute);
        $out = Attribute::buildAttribute(id:$attribute->id)->first();
        return response()->json(new AttributeResource($out,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }


    /**
     * @throws \Exception
     */
    public function delete_attribute(ElementType $element_type, Attribute $doomed_attribute): JsonResponse {

        if ($element_type->isInUse()) {

            throw new HexbatchPermissionException(__("msg.attribute_cannot_be_deleted_if_in_use",['ref'=>$doomed_attribute->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ATTRIBUTE_CANNOT_DELETE);
        }

        //todo put the delete attribute into the thing
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_NOT_IMPLEMENTED);
    }

    public function attribute_get(ElementType $element_type, Attribute $attribute,?int $levels = 2): JsonResponse {
        return response()->json(new AttributeResource($attribute,null,$levels), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function attributes_list(ElementType $element_type,?string $filter = null): JsonResponse {
        Utilities::ignoreVar($filter);
        $laravel_list = Attribute::buildAttribute(element_type_id: $element_type->id);
        $ret = $laravel_list->cursorPaginate();
        return (new AttributeCollection($ret))
            ->response()->setStatusCode(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function attribute_list_rules(ElementType $element_type,Attribute $attribute,?string $filter = null): JsonResponse {
        Utilities::ignoreVar($filter,$element_type);
        return response()->json(new AttributeRuleCollection($attribute->top_rule), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }


    public function attribute_new_rule(Request $request,ElementType $element_type,Attribute $attribute): JsonResponse {
        Utilities::ignoreVar($element_type); //checked in the middleware
        $mod_rule = AttributeRule::collectRule(collect: $request->collect(),owner_attr: $attribute);
        $out = AttributeRule::buildAttributeRule(id:$mod_rule->id)->first();
        return response()->json(new AttributeRuleResource($out,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }



    public function attribute_edit_rule(Request $request,ElementType $element_type,Attribute $attribute,AttributeRule $attribute_rule): JsonResponse {
        Utilities::ignoreVar($element_type); //checked in the middleware
        $mod_rule = AttributeRule::collectRule(collect: $request->collect(), rule: $attribute_rule,owner_attr: $attribute);
        $out = AttributeRule::buildAttributeRule(id:$mod_rule->id)->first();
        return response()->json(new AttributeRuleResource($out,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function attribute_delete_rule(ElementType $element_type,Attribute $attribute,AttributeRule $attribute_rule): JsonResponse {
        Utilities::ignoreVar($element_type,$attribute); //checked in the middleware
        if ($attribute_rule->isInUse()) {

            throw new HexbatchPermissionException(__("msg.attribute_cannot_be_deleted_if_in_use",['ref'=>$attribute_rule->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::RULE_CANNOT_DELETE);
        }
        $attribute_rule->delete();
        return response()->json(new AttributeRuleResource($attribute_rule,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }



    public function attribute_get_rule(ElementType $element_type,Attribute $attribute,AttributeRule $attribute_rule,?string $levels = null): JsonResponse {
        Utilities::ignoreVar($element_type); //checked in the middleware
        if ($attribute_rule->getAncestorAttribute?->id !== $attribute->id) {
            throw new HexbatchPermissionException(__("msg.rule_not_found",['ref'=>$attribute_rule->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::RULE_NOT_FOUND);
        }
        return response()->json(new AttributeRuleResource($attribute_rule,null,$levels), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

}
