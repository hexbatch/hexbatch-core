<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class UserGroupController extends Controller
{
    public function group_create(Request $request): JsonResponse {
        //todo create group,returns the group guid , required name
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT);
    }

    public function group_destroy(UserGroup $group): JsonResponse {
        //todo destroy group.Can only be deleted if not in use anywhere
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT);
    }

    public function group_member_add(UserGroup $group,Request $request): JsonResponse {
        //todo Adds membership to a single user
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT);
    }

    public function group_member_remove(UserGroup $group,Request $request): JsonResponse {
        //todo Removes membership for a single user
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT);
    }

    public function group_admin_add(UserGroup $group,Request $request): JsonResponse {
        //todo Add admin status for a single user
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT);
    }

    public function group_admin_remove(UserGroup $group,Request $request): JsonResponse {
        //todo Removes admin status for a single user, they are still member
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT);
    }

    public function group_list_members(UserGroup $group,Request $request): JsonResponse {
        //todo lists the membership and admins , can be iterator for next page
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT);
    }

    public function list_my_groups(Request $request): JsonResponse {
        //todo lists the groups by guid, that user is involved in , can be iterator for next page
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT);
    }
}
