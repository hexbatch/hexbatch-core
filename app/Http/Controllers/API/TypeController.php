<?php

namespace App\Http\Controllers\API;


use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Helpers\Attributes\AttributeGathering;
use App\Helpers\ElementTypes\TypeGathering;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;


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

    public function type_ping(): JsonResponse {
        return response()->json(['needs implementation'=>true], \Symfony\Component\HttpFoundation\Response::HTTP_ACCEPTED);
    }

    public function attribute_ping(): JsonResponse {
        return response()->json(['needs implementation'=>true], \Symfony\Component\HttpFoundation\Response::HTTP_ACCEPTED);
    }

    public function new_attribute(Request $request,ElementType $element_type, ?Attribute $parent_attribute=null): JsonResponse {
        //$attribute = (new AttributeGathering($request,$parent_attribute,$element_type) )->assign();
        //todo create attributes here first
        return response()->json(['needs implementation'=>true], \Symfony\Component\HttpFoundation\Response::HTTP_ACCEPTED);
    }

    public function edit_attribute(ElementType $element_type, Attribute $attribute): JsonResponse {
        return response()->json(['needs implementation'=>true], \Symfony\Component\HttpFoundation\Response::HTTP_ACCEPTED);
    }

    public function copy_attribute(ElementType $element_type, Attribute $source_attribute): JsonResponse {
        return response()->json(['needs implementation'=>true], \Symfony\Component\HttpFoundation\Response::HTTP_ACCEPTED);
    }

    public function delete_attribute(ElementType $element_type, Attribute $doomed_attribute): JsonResponse {
        return response()->json(['needs implementation'=>true], \Symfony\Component\HttpFoundation\Response::HTTP_ACCEPTED);
    }

    public function attribute_get(ElementType $element_type, Attribute $attribute): JsonResponse {
        return response()->json(['needs implementation'=>true], \Symfony\Component\HttpFoundation\Response::HTTP_ACCEPTED);
    }

    public function attributes_list(ElementType $element_type,?string $filter = null): JsonResponse {
        return response()->json(['needs implementation'=>true], \Symfony\Component\HttpFoundation\Response::HTTP_ACCEPTED);
    }

    public function attribute_list_rules(ElementType $element_type,?string $filter = null): JsonResponse {
        return response()->json(['needs implementation'=>true], \Symfony\Component\HttpFoundation\Response::HTTP_ACCEPTED);
    }

    public function attribute_new_rule(ElementType $element_type,Attribute $attribute): JsonResponse {
        return response()->json(['needs implementation'=>true], \Symfony\Component\HttpFoundation\Response::HTTP_ACCEPTED);
    }

    public function attribute_clear_rules(ElementType $element_type,Attribute $attribute): JsonResponse {
        return response()->json(['needs implementation'=>true], \Symfony\Component\HttpFoundation\Response::HTTP_ACCEPTED);
    }

    public function attribute_edit_rule(ElementType $element_type,Attribute $attribute,AttributeRule $attribute_rule): JsonResponse {
        return response()->json(['needs implementation'=>true], \Symfony\Component\HttpFoundation\Response::HTTP_ACCEPTED);
    }

    public function attribute_delete_rule(ElementType $element_type,Attribute $attribute,AttributeRule $attribute_rule): JsonResponse {
        return response()->json(['needs implementation'=>true], \Symfony\Component\HttpFoundation\Response::HTTP_ACCEPTED);
    }

    public function attribute_get_rule(ElementType $element_type,Attribute $attribute,AttributeRule $attribute_rule): JsonResponse {
        return response()->json(['needs implementation'=>true], \Symfony\Component\HttpFoundation\Response::HTTP_ACCEPTED);
    }

}
