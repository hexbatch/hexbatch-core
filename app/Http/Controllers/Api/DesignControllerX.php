<?php

namespace App\Http\Controllers\Api;


use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;


use App\Http\Resources\AttributeCollection;
use App\Http\Resources\AttributeResource;
use App\Http\Resources\AttributeRuleResource;
use App\Http\Resources\ElementTypeCollection;
use App\Http\Resources\ElementTypeResource;
use App\Models\Attribute;
use App\Models\AttributeRule;
use App\Models\ElementType;
use App\Models\ServerEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class DesignControllerX extends Controller
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

        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_NOT_IMPLEMENTED);
    }

    public function attribute_get(ElementType $element_type, Attribute $attribute,?int $levels = 2): JsonResponse {
        Utilities::ignoreVar($element_type);
        return response()->json(new AttributeResource($attribute,null,$levels), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function attributes_list(ElementType $element_type,?string $filter = null): JsonResponse {
        Utilities::ignoreVar($filter);
        $laravel_list = Attribute::buildAttribute(type_id: $element_type->id);
        $ret = $laravel_list->cursorPaginate();
        return (new AttributeCollection($ret))
            ->response()->setStatusCode(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }


    /*
     * ******************************************************************************
     *                           Rules                                              *
     * ******************************************************************************
     */

    public function attribute_list_rules(ElementType $element_type,Attribute $attribute): JsonResponse {
        Utilities::ignoreVar($element_type);
        return response()->json(new AttributeRuleResource($attribute->attached_event->top_rule), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }


    /*
     * create rule tree (can be all or add in a parent)
     * edit rule tree (all in one go, can be a subtree)
     * edit rule node (cannot change parents or children)
     * delete rule tree (trims children)
     */
    public function create_rules(Request $request, ElementType $element_type, Attribute $attribute): JsonResponse {
        Utilities::ignoreVar($element_type); //checked in the middleware
        if ($attribute->attached_event) {
            throw new HexbatchNotPossibleException(
                __('msg.rules_already_exist',['ref'=>$attribute->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::RULE_NOT_FOUND
            );
        }
        $mod_rule = ServerEvent::collectEvent(collect: $request->collect(), owner: $attribute);
        return response()->json(new AttributeRuleResource($mod_rule,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    /**
     * @throws \Exception
     */
    public function update_rules(Request $request, ElementType $element_type, Attribute $attribute): JsonResponse {
        Utilities::ignoreVar($element_type); //checked in the middleware
        $attribute->attached_event->top_rule->delete_subtree();
        $mod_rule = ServerEvent::collectEvent(collect: $request->collect(), owner: $attribute);
        return response()->json(new AttributeRuleResource($mod_rule,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }



    public function add_rule_subtree(Request $request, ElementType $element_type, Attribute $attribute, AttributeRule $attribute_rule): JsonResponse {
        Utilities::ignoreVar($element_type); //checked in the middleware
        $mod_rule = AttributeRule::collectRule(collect: $request->collect(), parent_rule: $attribute_rule, owner_event: $attribute->attached_event);
        return response()->json(new AttributeRuleResource($mod_rule,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }


    /**
     * @throws \Exception
     */
    public function edit_rule(Request $request, ElementType $element_type, Attribute $attribute, AttributeRule $attribute_rule): JsonResponse {
        Utilities::ignoreVar($element_type,$attribute); //checked in the middleware
        $attribute_rule->editRule(collect: $request->collect());
        $out = AttributeRule::buildAttributeRule(id:$attribute_rule->id)->first();
        return response()->json(new AttributeRuleResource($out,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }


    /**
     * @throws \Exception
     */
    public function delete_rules(ElementType $element_type, Attribute $attribute): JsonResponse {
        Utilities::ignoreVar($element_type,$attribute); //checked in the middleware
        $attribute->attached_event->top_rule->delete_subtree();
        return response()->json(new AttributeRuleResource($attribute->attached_event->top_rule,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    /**
     * @throws \Exception
     */
    public function delete_rule_subtree(ElementType $element_type, Attribute $attribute, AttributeRule $attribute_rule): JsonResponse {
        Utilities::ignoreVar($element_type,$attribute); //checked in the middleware
        $attribute_rule->delete_subtree();
        return response()->json(new AttributeRuleResource($attribute_rule,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }



    public function attribute_get_rule(ElementType $element_type,Attribute $attribute,AttributeRule $attribute_rule,?string $levels = null): JsonResponse {
        Utilities::ignoreVar($element_type,$attribute); //checked in the middleware
        return response()->json(new AttributeRuleResource($attribute_rule,null,$levels), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function rule_test(Request $request, ElementType $element_type, Attribute $attribute): JsonResponse {
        Utilities::ignoreVar($request,$element_type,$attribute); //checked in the middleware
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_NOT_IMPLEMENTED);
    }

}
