<?php

namespace App\Http\Controllers\API;

use App\Exceptions\HexbatchNameConflictException;
use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserGroupCollection;
use App\Http\Resources\UserGroupMemberCollection;
use App\Http\Resources\UserGroupMemberResource;
use App\Http\Resources\UserGroupResource;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserGroupMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;


class UserGroupController extends Controller
{

    protected function adminCheck(UserGroup $group) {

        $user = Utilities::getTypeCastedAuthUser();
        if (!$group->isAdmin($user?->id)) {
            throw new HexbatchPermissionException(__("msg.group_only_admin_changes_membership"),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ONLY_ADMINS_CAN_CHANGE_MEMBERSHIP);
        }
    }

    protected function memberCheck(UserGroup $group,User $da_user) : UserGroupMember {
        $member = $group->isMember($da_user->id);
        if (!$member) {
            throw new HexbatchPermissionException(__("msg.group_this_member_does_not_exist",["username"=>$da_user->username]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::GROUP_OPERATION_MISSING_MEMBER);
        }
        return $member;
    }

    protected function ownerCheck(UserGroup $group) {

        $user = auth()->user();
        if (!$group->user_id === ($user?->id??null)) {
            throw new HexbatchPermissionException(__("msg.group_only_owner_can_change_admins"),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ONLY_OWNERS_CAN_CHANGE_ADMINS);
        }
    }

    /**
     * @throws ValidationException
     */
    public function group_create(?string $group_name): JsonResponse {
        $group = new UserGroup();
        $group->setGroupName($group_name);
        $user = Utilities::getTypeCastedAuthUser();
        $conflict =  UserGroup::where('user_id', $user?->id)->where('group_name',$group->group_name)->first();
        if ($conflict) {
            throw new HexbatchNameConflictException(__("msg.unique_resource_name_per_user",['resource_name'=>$group->group_name]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::RESOURCE_NAME_UNIQUE_PER_USER);
        }
        $group->user_id = $user->id;
        $group->save();
        $group->addMember($user->id,true);
        $group->refresh();
        //todo allow to define new fields for parent (make sure owns parent when this happens), and combine strategy
        return response()->json(new UserGroupResource($group), \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
    }

    public function group_destroy(UserGroup $group): JsonResponse {

        $user = Utilities::getTypeCastedAuthUser();
        if ($group->user_id !== $user->id) {
            throw new HexbatchPermissionException(__("msg.group_only_owner_can_delete"),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ONLY_OWNERS_CAN_DELETE_GROUPS);
        }
        if ($group->isInUse()) {
            throw new HexbatchPermissionException(__("msg.group_can_only_be_deleted_if_not_in_use"),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ONLY_OWNERS_CAN_DELETE_GROUPS);
        }
        $group->delete();
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT);
    }

    public function group_member_add(UserGroup $group,User $some_user): JsonResponse {
        $this->adminCheck($group);
        if ($existing_member = $group->isMember($some_user->id)) {
            return response()->json(new UserGroupMemberResource($existing_member), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
        }
        $new_member = $group->addMember($some_user->id);
        //todo need to update any derived attribute user groups
        return response()->json(new UserGroupMemberResource($new_member), \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
    }

    public function group_member_remove(UserGroup $group,User $some_user): JsonResponse {
        $this->adminCheck($group);
        $this->memberCheck($group,$some_user);
        $old_member = $group->removeMember($some_user->id);
        //todo need to update any derived attribute user groups
        return response()->json(new UserGroupMemberResource($old_member), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function group_admin_add(UserGroup $group,User $some_user): JsonResponse {
        $this->ownerCheck($group);
        $existing_member = $this->memberCheck($group,$some_user);
        if ($existing_member->is_admin) {
            return response()->json(new UserGroupMemberResource($existing_member), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
        }
        $existing_member->is_admin = true;
        $existing_member->save();
        return response()->json(new UserGroupMemberResource($existing_member), \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
    }

    public function group_admin_remove(UserGroup $group,User $some_user): JsonResponse {
        $this->ownerCheck($group);
        $existing_member = $this->memberCheck($group,$some_user);
        if ($existing_member->is_admin) {
            $existing_member->is_admin = false;
            $existing_member->save();
            return response()->json(new UserGroupMemberResource($existing_member), \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
        }
        return response()->json(new UserGroupMemberResource($existing_member), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function group_list_members(UserGroup $group) {
        $ret = $group->group_members()->cursorPaginate();
        return (new UserGroupMemberCollection($ret))
            ->response()->setStatusCode(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function list_my_groups(): JsonResponse {
        $ret = UserGroup::buildGroup(Utilities::getTypeCastedAuthUser()?->id)->cursorPaginate();
        return (new UserGroupCollection($ret))
            ->response()->setStatusCode(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function group_get(UserGroup $group) {
        $user = Utilities::getTypeCastedAuthUser();
        $this->memberCheck($group,$user);
        $ret = UserGroup::buildGroup($user->id)->first();
        return response()->json(new UserGroupResource($ret,null,2), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }
}
