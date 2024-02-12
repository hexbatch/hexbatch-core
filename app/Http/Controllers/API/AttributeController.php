<?php

namespace App\Http\Controllers\API;

use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Attributes\Build\AttributeBinaryOptions;
use App\Helpers\Attributes\Build\AttributeBounds;
use App\Helpers\Attributes\Build\AttributeMetaGathering;
use App\Helpers\Attributes\Build\AttributePermissionGathering;
use App\Helpers\Attributes\Build\AttributeRuleGathering;
use App\Helpers\Attributes\Build\AttributeValue;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttributeCollection;
use App\Http\Resources\AttributeResource;
use App\Models\Attribute;
use App\Models\AttributeUserGroup;
use App\Models\Enums\Attributes\AttributePingType;
use App\Models\Enums\Attributes\AttributeUserGroupType;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


/*
| Get    | attribute/:id/bounds/ping   |            | Determines if the attribute is in bounds              | Location, Time and Set                                                |
 */

class AttributeController extends Controller
{
    /**
     * @uses Attribute::attribute_owner()
     */
    protected function adminCheck(Attribute $att) {
        $user = auth()->user();
        $att->attribute_owner->checkAdminGroup($user->id);
    }

    public function attribute_get(Attribute $attribute,?string $full = null) {
        $this->adminCheck($attribute);
        $out = Attribute::buildAttribute(id: $attribute->id)->first();
        $n_level = (int)$full;
        if ($n_level <= 0) { $n_level =1;}
        return response()->json(new AttributeResource($out,null,$n_level), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }



    /**
     * @param Request $request
     * @param Attribute $attribute
     * @param AttributePingType $attribute_ping_type
     * @return JsonResponse
     * @throws ValidationException
     */
    public function attribute_ping(Request $request, Attribute $attribute, AttributePingType $attribute_ping_type) {

        $location_to_ping = $request->get('location_ping');
        $shape_to_ping = $request->get('shape_ping');
        $time_string = $request->get('time_string');
        $user_lookup = $request->get('user');

        $location_to_ping_json = '';
        if (!empty($location_to_ping)) {
            $location_to_ping_json = json_encode($location_to_ping);
        }

        $shape_to_ping_json = '';
        if (!empty($shape_to_ping)) {
            $shape_to_ping_json = json_encode($shape_to_ping);
        }

        $check_user = null;
        if ($user_lookup) {
            $check_user = (new User)->resolveRouteBinding($user_lookup);
        }


        $this->adminCheck($attribute);

        $ret = [];
        if ($attribute_ping_type === AttributePingType::ALL || $attribute_ping_type === AttributePingType::ALL_TIME || $attribute_ping_type === AttributePingType::READ_TIME) {
            if ($attribute->read_time_bound) {
                $ret['read_time'] = $attribute->read_time_bound?->ping($time_string);
            }
        }

        if ($attribute_ping_type === AttributePingType::ALL || $attribute_ping_type === AttributePingType::ALL_TIME || $attribute_ping_type === AttributePingType::WRITE_TIME) {
            if($attribute->write_time_bound) {
                $ret['write_time'] = $attribute->write_time_bound?->ping($time_string);
            }
        }

        if ($attribute_ping_type === AttributePingType::ALL || $attribute_ping_type === AttributePingType::ALL_MAP || $attribute_ping_type === AttributePingType::READ_MAP) {
            if (empty($location_to_ping )) {
                throw new HexbatchNotPossibleException(__("msg.attribute_ping_missing_data"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_PING_DATA_MISSING);
            }
            if($attribute->read_map_bound) {
                $ret['read_map'] = $attribute->read_map_bound?->ping($location_to_ping_json);
            }
        }

        if ($attribute_ping_type === AttributePingType::ALL || $attribute_ping_type === AttributePingType::ALL_MAP || $attribute_ping_type === AttributePingType::WRITE_MAP) {
            if (!$location_to_ping) {
                throw new HexbatchNotPossibleException(__("msg.attribute_ping_missing_data"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_PING_DATA_MISSING);
            }
            if($attribute->write_map_bound) {
                $ret['write_map'] = $attribute->write_map_bound?->ping($location_to_ping_json);
            }
        }

        if ($attribute_ping_type === AttributePingType::ALL || $attribute_ping_type === AttributePingType::ALL_SHAPE || $attribute_ping_type === AttributePingType::READ_SHAPE) {
            if (!$shape_to_ping) {
                throw new HexbatchNotPossibleException(__("msg.attribute_ping_missing_data"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_PING_DATA_MISSING);
            }
            if($attribute->read_shape_bound) {
                $ret['read_shape'] = $attribute->read_shape_bound?->ping($shape_to_ping_json);
            }
        }

        if ($attribute_ping_type === AttributePingType::ALL || $attribute_ping_type === AttributePingType::ALL_SHAPE || $attribute_ping_type === AttributePingType::WRITE_SHAPE) {
            if (!$shape_to_ping) {
                throw new HexbatchNotPossibleException(__("msg.attribute_ping_missing_data"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_PING_DATA_MISSING);
            }
            if($attribute->write_shape_bound) {
                $ret['write_shape'] = $attribute->write_shape_bound?->ping($shape_to_ping_json);
            }
        }

        if ($attribute_ping_type === AttributePingType::ALL || $attribute_ping_type === AttributePingType::ALL_USER || $attribute_ping_type === AttributePingType::READ_USER) {
            if (!$check_user) {
                 throw new HexbatchNotPossibleException(__("msg.attribute_ping_missing_data"),
                     \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                     RefCodes::ATTRIBUTE_PING_DATA_MISSING);
            }
            /**
             * @var AttributeUserGroup $read_group
             */
            $read_group = AttributeUserGroup::where('group_parent_attribute_id',$attribute->id)
                ->where('group_type',AttributeUserGroupType::READ->value)
                /** @uses AttributeUserGroup::target_user_group() */
                ->with('target_user_group')
                ->first();
            if($read_group) {
                $ret['read_user'] = (bool)$read_group->target_user_group->isMember($check_user->id);
            }
        }

        if ($attribute_ping_type === AttributePingType::ALL || $attribute_ping_type === AttributePingType::ALL_USER || $attribute_ping_type === AttributePingType::WRITE_USER) {
            if (!$check_user) {
                throw new HexbatchNotPossibleException(__("msg.attribute_ping_missing_data"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_PING_DATA_MISSING);
            }
            /**
             * @var AttributeUserGroup $write_group
             */
            $write_group = AttributeUserGroup::where('group_parent_attribute_id',$attribute->id)
                ->where('group_type',AttributeUserGroupType::WRITE->value)
                /** @uses AttributeUserGroup::target_user_group() */
                ->with('target_user_group')
                ->first();
            if($write_group) {
                $ret['write_user'] = (bool)$write_group->target_user_group->isMember($check_user->id);
            }
        }

        if ($attribute_ping_type === AttributePingType::ALL || $attribute_ping_type === AttributePingType::ALL_USER || $attribute_ping_type === AttributePingType::USAGE_USER) {
            if (!$check_user) {
                throw new HexbatchNotPossibleException(__("msg.attribute_ping_missing_data"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_PING_DATA_MISSING);
            }
            /**
             * @var AttributeUserGroup $write_group
             */
            $write_group = AttributeUserGroup::where('group_parent_attribute_id',$attribute->id)
                ->where('group_type',AttributeUserGroupType::WRITE->value)
                /** @uses AttributeUserGroup::target_user_group() */
                ->with('target_user_group')
                ->first();
            if($write_group) {
                $ret['usage_user'] = (bool)$write_group->target_user_group->isMember($check_user->id);
            }
        }

        $resp = \Symfony\Component\HttpFoundation\Response::HTTP_OK;
        foreach ($ret as $part) {
            if (!$part) {
                $resp = \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND;
                break;
            }
        }


        return response()->json(['attribute_id'=>$attribute->id,'results'=>$ret], $resp);
    }

    public function attribute_list_managed(?User $user = null) {
        $logged_user = auth()->user();
        if (!$user) {$user = $logged_user;}
        $out = Attribute::buildAttribute(admin_user_id: $user->id)->cursorPaginate();
        return response()->json(new AttributeCollection($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function attribute_list_usage(?User $user = null) {
        $logged_user = auth()->user();
        if (!$user) {$user = $logged_user;}
        $out = Attribute::buildAttribute(usage_user_id: $user->id)->cursorPaginate();
        return response()->json(new AttributeCollection($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function attribute_delete(Attribute $attribute) {
        $this->adminCheck($attribute);
        $attribute->checkIsInUse();
        $out = Attribute::buildAttribute(id: $attribute->id)->first();
        $attribute->delete();
        return response()->json(new AttributeResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }


    /**
     * @param Attribute $attribute
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws \Exception
     */
    public function attribute_edit_patch(Attribute $attribute, Request $request) {
        $this->adminCheck($attribute);

        try {
            DB::beginTransaction();
            // if this is in use then can only edit retired, meta
            // otherwise can edit all but the ownership
            if ($attribute->isInUse()) {
                (new AttributeMetaGathering($request) )->assign($attribute);
            } else {
                $user = auth()->user();
                $some_name = $request->request->getString('attribute_name');

                if ($some_name && $some_name !== $attribute->attribute_name) {
                    $attribute->setName($request->request->getString('attribute_name'),$user);
                }
                $some_parent = $request->request->getString('parent_attribute');
                if ($some_parent) {
                    $attribute->setParent($some_parent);
                }

                $this->updateAllAttribute($attribute,$request);
            }

            if ($request->request->has('is_retired')) {
                $attribute->is_retired = Utilities::boolishToBool($request->request->get('is_retired'));
                $attribute->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }



        $out = Attribute::buildAttribute(id: $attribute->id)->first();

        return response()->json(new AttributeResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    /**
     * @throws \Exception
     */
    protected function updateAllAttribute(Attribute $attribute, Request $request) {
        (new AttributeBinaryOptions($request) )->assign($attribute);
        (new AttributeBounds($request) )->assign($attribute);


        $attribute->save();

        (new AttributeValue(request:$request,attribute: $attribute) )->assign($attribute);
        (new AttributeMetaGathering($request) )->assign($attribute);
        (new AttributePermissionGathering($request) )->assign($attribute);
        (new AttributeRuleGathering($request) )->assign($attribute);
    }

    /**
     * @throws ValidationException
     * @throws \Exception
     */
    public function attribute_create(Request $request): JsonResponse {


        try {
            DB::beginTransaction();
            // if this is in use then can only edit retired, meta
            // otherwise can edit all but the ownership
            $attribute = new Attribute();
            $user = auth()->user();
            $attribute->setName($request->request->getString('attribute_name'),$user);
            $attribute->setParent($request->request->getString('parent_attribute'));
            $attribute->user_id = $user->id;
            $this->updateAllAttribute($attribute,$request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $out = Attribute::buildAttribute(id: $attribute->id)->first();
        return response()->json(new AttributeResource($out,null,2), \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
    }
}
