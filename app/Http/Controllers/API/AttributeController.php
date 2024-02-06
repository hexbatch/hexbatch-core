<?php

namespace App\Http\Controllers\API;

use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Attributes\AttributeBinaryOptions;
use App\Helpers\Attributes\AttributeBounds;
use App\Helpers\Attributes\AttributeMetaGathering;
use App\Helpers\Attributes\AttributePermissionGathering;
use App\Helpers\Attributes\AttributeRuleGathering;
use App\Helpers\Attributes\AttributeValue;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttributeCollection;
use App\Http\Resources\AttributeResource;
use App\Http\Resources\LocationBoundResource;
use App\Models\Attribute;
use App\Models\AttributeUserGroup;
use App\Models\Enums\AttributePingType;
use App\Models\Enums\AttributeUserGroupType;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $b_brief = !$full;
        return response()->json(new AttributeResource($out,$b_brief), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }



    /**
     * @param Request $request
     * @param Attribute $attribute
     * @param AttributePingType $ping_type
     * @return JsonResponse
     * @throws ValidationException
     */
    public function attribute_ping(Request $request, Attribute $attribute, AttributePingType $ping_type) {

        $location_json_to_ping = $request->query->getString('location_json_to_ping');
        $shape_json_to_ping = $request->query->getString('shape_json_to_ping');
        $time_string = $request->query->getString('time_string');
        $user_lookup = $request->query->getString('user');

        $check_user = null;
        if ($user_lookup) {
            $check_user = (new User)->resolveRouteBinding($user_lookup);
        }


        $this->adminCheck($attribute);

        $ret = [];
        if ($ping_type === AttributePingType::ALL || $ping_type === AttributePingType::ALL_TIME || $ping_type === AttributePingType::READ_TIME) {
            if ($attribute->read_time_bound) {
                $ret['read_time'] = $attribute->read_time_bound?->ping($time_string);
            }
        }

        if ($ping_type === AttributePingType::ALL || $ping_type === AttributePingType::ALL_TIME || $ping_type === AttributePingType::WRITE_TIME) {
            if($attribute->write_time_bound) {
                $ret['write_time'] = $attribute->write_time_bound?->ping($time_string);
            }
        }

        if ($ping_type === AttributePingType::ALL || $ping_type === AttributePingType::ALL_MAP || $ping_type === AttributePingType::READ_MAP) {
            if (!$location_json_to_ping) {
                throw new HexbatchNotPossibleException(__("msg.attribute_ping_missing_data"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_PING_DATA_MISSING);
            }
            if($attribute->read_map_bound) {
                $ret['read_map'] = $attribute->read_map_bound?->ping($location_json_to_ping);
            }
        }

        if ($ping_type === AttributePingType::ALL || $ping_type === AttributePingType::ALL_MAP || $ping_type === AttributePingType::WRITE_MAP) {
            if (!$location_json_to_ping) {
                throw new HexbatchNotPossibleException(__("msg.attribute_ping_missing_data"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_PING_DATA_MISSING);
            }
            if($attribute->write_map_bound) {
                $ret['write_map'] = $attribute->write_map_bound?->ping($location_json_to_ping);
            }
        }

        if ($ping_type === AttributePingType::ALL || $ping_type === AttributePingType::ALL_SHAPE || $ping_type === AttributePingType::READ_SHAPE) {
            if (!$shape_json_to_ping) {
                throw new HexbatchNotPossibleException(__("msg.attribute_ping_missing_data"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_PING_DATA_MISSING);
            }
            if($attribute->read_shape_bound) {
                $ret['read_shape'] = $attribute->read_shape_bound?->ping($shape_json_to_ping);
            }
        }

        if ($ping_type === AttributePingType::ALL || $ping_type === AttributePingType::ALL_SHAPE || $ping_type === AttributePingType::WRITE_SHAPE) {
            if (!$shape_json_to_ping) {
                throw new HexbatchNotPossibleException(__("msg.attribute_ping_missing_data"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_PING_DATA_MISSING);
            }
            if($attribute->write_shape_bound) {
                $ret['write_shape'] = $attribute->write_shape_bound?->ping($shape_json_to_ping);
            }
        }

        if ($ping_type === AttributePingType::ALL || $ping_type === AttributePingType::ALL_USER || $ping_type === AttributePingType::READ_USER) {
            if (!$check_user) {
                 throw new HexbatchNotPossibleException(__("msg.attribute_ping_missing_data"),
                     \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                     RefCodes::ATTRIBUTE_PING_DATA_MISSING);
            }
            /**
             * @var UserGroup $read_group
             */
            $read_group = AttributeUserGroup::where('group_parent_attribute_id',$attribute->id)
                ->where('group_type',AttributeUserGroupType::READ->value)
                /** @uses AttributeUserGroup::target_user_group() */
                ->with('target_user_group')
                ->first();
            if($read_group) {
                $ret['read_user'] = (bool)$read_group->isMember($check_user->id);
            }
        }

        if ($ping_type === AttributePingType::ALL || $ping_type === AttributePingType::ALL_USER || $ping_type === AttributePingType::WRITE_USER) {
            if (!$check_user) {
                throw new HexbatchNotPossibleException(__("msg.attribute_ping_missing_data"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_PING_DATA_MISSING);
            }
            /**
             * @var UserGroup $write_group
             */
            $write_group = AttributeUserGroup::where('group_parent_attribute_id',$attribute->id)
                ->where('group_type',AttributeUserGroupType::WRITE->value)
                /** @uses AttributeUserGroup::target_user_group() */
                ->with('target_user_group')
                ->first();
            if($write_group) {
                $ret['write_user'] = (bool)$write_group->isMember($check_user->id);
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

    public function attribute_list_manage(?User $user = null) {
        $logged_user = auth()->user();
        if (!$user) {$user = $logged_user;}
        $out = Attribute::buildAttribute(admin_user_id: $user->id)->cursorPaginate();
        return response()->json(new AttributeCollection($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function attribute_delete(Attribute $attribute) {
        $this->adminCheck($attribute);
        $attribute->checkIsInUse();
        $out = Attribute::buildAttribute(id: $attribute->id)->first();
        $attribute->delete();
        return response()->json(new LocationBoundResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
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


        // if this is in use then can only edit retired, meta
        // otherwise can edit all but the ownership
        if ($attribute->isInUse()) {
            (new AttributeMetaGathering($request) )->assign($attribute);
        } else {
            $user = auth()->user();
            $attribute->setName($request->request->getString('attribute_name'),$user);
            $attribute->setParent($request->request->getString('parent_attribute'));
            $this->updateAllAttribute($attribute,$request);
        }

        if ($request->request->has('is_retired')) {
            $attribute->is_retired = Utilities::boolishToBool($request->request->get('is_retired'));
            $attribute->save();
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

        /*
         * parent,user,name,retired,
         * bounds (all 6),
            requirements ( required_siblings,forbidden_siblings,allergies,affinities,set read,set write) is_read_policy_all is_write_policy_all
            meta (any given)
            permissions(usage,read,write)
            value (all the fields)
            options
         */
        $attribute = new Attribute();
        $user = auth()->user();
        $attribute->setName($request->request->getString('attribute_name'),$user);
        $attribute->setParent($request->request->getString('parent_attribute'));
        $attribute->user_id = $user->id;
        $this->updateAllAttribute($attribute,$request);


        $out = Attribute::buildAttribute(id: $attribute->id)->first();
        return response()->json(new AttributeResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
    }
}
