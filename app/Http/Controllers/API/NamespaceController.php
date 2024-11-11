<?php

namespace App\Http\Controllers\API;

use App\Helpers\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;
use App\Sys\Res\Types\Stk\Root;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class NamespaceController extends Controller {
    #[ApiTypeMarker( Root\Api\Namespace\Show::class)]
    public function show_namespace() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Namespace\ShowPublic::class)]
    public function show_namespace_public() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Namespace\ListAll::class)]
    public function list_namespaces() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Namespace\EditPromotion::class)]
    public function edit_promotion() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Namespace\Destroy::class)]
    public function destroy_namespace() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Namespace\Create::class)]
    public function create_namespace() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Namespace\Promote::class)]
    public function promote_namespace() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Namespace\Purge::class)]
    public function purge_namespace() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Namespace\AddAdmin::class)]
    public function add_admin() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Namespace\ListAdmins::class)]
    public function list_admins() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Namespace\RemoveAdmin::class)]
    public function remove_admin() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Namespace\PurgeAdmin::class)]
    public function purge_admin() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Namespace\PromoteAdmin::class)]
    public function promote_admin() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[ApiTypeMarker( Root\Api\Namespace\AddMember::class)]
    public function add_member() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Namespace\RemoveMember::class)]
    public function remove_member() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Namespace\PromoteMember::class)]
    public function promote_member() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Namespace\PurgeMember::class)]
    public function purge_member() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Namespace\ListMembers::class)]
    public function list_members() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


}
