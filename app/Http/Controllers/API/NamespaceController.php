<?php

namespace App\Http\Controllers\API;


use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;

use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserNamespaceCollection;
use App\Http\Resources\UserNamespaceMemberCollection;
use App\Http\Resources\UserNamespaceMemberResource;

use App\Http\Resources\UserNamespaceResource;
use App\Models\Server;
use App\Models\User;
use App\Models\UserNamespaceMember;
use App\Models\UserNamespace;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class NamespaceController extends Controller
{

    public function list_members(UserNamespace $user_namespace ): JsonResponse {
        $ret = UserNamespaceMember::buildGroupMembers(namespace_parent_id: $user_namespace->id)->cursorPaginate();
        return (new UserNamespaceMemberCollection($ret))
            ->response()->setStatusCode(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }


    public function group_member_add(UserNamespace $user_namespace,UserNamespace $ns_to_be_added): JsonResponse {
        $existing_member = $user_namespace->isNamespaceMember($ns_to_be_added);
        if (!$existing_member) {
            $new_member = $user_namespace->addMember($ns_to_be_added);
            return response()->json(new UserNamespaceMemberResource($new_member), \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
        } else  {
            return response()->json(new UserNamespaceMemberResource($existing_member), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
        }
    }

    public function group_member_remove(UserNamespace $user_namespace,UserNamespace $ns_to_be_removed): JsonResponse {
        $existing_member = $user_namespace->isNamespaceMember($ns_to_be_removed);
        if (!$existing_member?->is_admin) {
            throw new HexbatchNotPossibleException(__("msg.namespace_member_not_found", ['ref' => $ns_to_be_removed->getName(),'me'=>$user_namespace->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::NAMESPACE_MEMBER_MISSING_ISSUE);

        }
        $user_namespace->removeMember($ns_to_be_removed);
        return response()->json(new UserNamespaceMemberResource($ns_to_be_removed), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function group_admin_add(UserNamespace $user_namespace,UserNamespace $ns_to_be_added): JsonResponse {

        $existing_member = $user_namespace->isNamespaceMember($ns_to_be_added);
        if (!$existing_member) {
            $new_member = $user_namespace->addMember($ns_to_be_added,true);
            return response()->json(new UserNamespaceMemberResource($new_member), \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
        } else  if ($existing_member->is_admin) {
            return response()->json(new UserNamespaceMemberResource($existing_member), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
        } else {
            $existing_member->is_admin = true;
            $existing_member->save();
            return response()->json(new UserNamespaceMemberResource($existing_member), \Symfony\Component\HttpFoundation\Response::HTTP_ACCEPTED);
        }
    }

    public function group_admin_remove(UserNamespace $user_namespace,UserNamespace $ns_to_be_removed): JsonResponse {

        $existing_member = $user_namespace->isNamespaceAdmin($ns_to_be_removed);
        if (!$existing_member?->is_admin) {
            throw new HexbatchNotPossibleException(__("msg.namespace_admin_not_found", ['ref' => $ns_to_be_removed->getName(),'me'=>$user_namespace->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::NAMESPACE_MEMBER_MISSING_ISSUE);

        }

        $existing_member->is_admin = false;
        $existing_member->save();
        return response()->json(new UserNamespaceMemberResource($ns_to_be_removed), \Symfony\Component\HttpFoundation\Response::HTTP_OK);

    }


    public function list_my_namespaces(): JsonResponse {
        $ret = UserNamespace::buildNamespace(user_id: Utilities::getTypeCastedAuthUser()?->id)->cursorPaginate();
        return (new UserNamespaceCollection($ret))
            ->response()->setStatusCode(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }


    public function create_namespace(Request $request,?Server $server): JsonResponse {
        $user = Utilities::getTypeCastedAuthUser();
        $namespace = UserNamespace::createNamespace($request->request->getString('namespace_name'),$user,$server);
        return response()->json(new UserNamespaceResource($namespace), \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
    }

    public function transfer_namespace(Request $request,User $user): JsonResponse {
        Utilities::ignoreVar($request,$user);
        //todo implement transfer, new s.a in the private to allow the transfer to the user ref stored as the value
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_SERVICE_UNAVAILABLE);
    }


    public function get_namespace(UserNamespace $user_namespace,?int $levels = 3): JsonResponse {
        return (new UserNamespaceResource($user_namespace,null,$levels))
            ->response()->setStatusCode(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function destroy_namespace(UserNamespace $user_namespace): JsonResponse {

        if ($user_namespace->isDefault()) {
            throw new HexbatchNotPossibleException(__("msg.namespace_cannot_delete_default",
                ['ref' => $user_namespace->getName(),'user_name'=>$user_namespace->owner_user->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::NAMESPACE_CANNOT_DELETE_CORE_PARTS);
        }

        if ($user_namespace->isInUse()) {
            throw new HexbatchNotPossibleException(__("msg.namespace_cannot_delete_while_in_use",
                ['ref' => $user_namespace->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::RESOURCE_CANNOT_DELETE_IN_USE);
        }
        $user_namespace->freeResources();
        $user_namespace->purgeHome();
        $user_namespace->delete();

        return (new UserNamespaceResource($user_namespace,null,2))
            ->response()->setStatusCode(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

}
