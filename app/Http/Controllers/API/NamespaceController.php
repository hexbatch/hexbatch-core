<?php

namespace App\Http\Controllers\API;

use App\Helpers\Annotations\Access\TypeOfAccessMarker;
use App\Helpers\Annotations\ApiAccessMarker;
use App\Helpers\Annotations\ApiEventMarker;
use App\Helpers\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;
use App\Sys\Res\Types\Stk\Root;
use Symfony\Component\HttpFoundation\Response as CodeOf;
use OpenApi\Attributes as OA;
use App\Sys\Res\Types\Stk\Root\Evt;

class NamespaceController extends Controller {
    #[OA\Get(
        path: '/api/v1/namespaces/show_namespace',
        operationId: 'core.namespaces.show_namespace',
        description: "Namespace members can run this to see the owner, the name, the count of admins, members, types, elements ".
        "\n Will show a list of the first admins (not a complete list)",
        summary: 'Shows a summary of the namespace',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Namespace\Show::class)]
    public function show_namespace() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Get(
        path: '/api/v1/namespaces/show_namespace_public',
        operationId: 'core.namespaces.show_namespace_public',
        description: "Anyone can run this to see some info about the namespace. Will only show public data",
        summary: 'Shows a summary of the namespace',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::IS_PUBLIC)]
    #[ApiTypeMarker( Root\Api\Namespace\ShowPublic::class)]
    public function show_namespace_public() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Get(
        path: '/api/v1/namespaces/list_namespaces',
        operationId: 'core.namespaces.list_namespaces',
        description: "Will show owned, admin and member status of all namespaces this caller is part of. Can filter by handle or namespace name",
        summary: 'Shows all the namespaces this caller is part of',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Namespace\ListAll::class)]
    public function list_namespaces() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Patch(
        path: '/api/v1/namespaces/edit_promotion',
        operationId: 'core.namespaces.edit_promotion',
        description: "System can set data in namespaces without events going off. ".
            "\n can set new homesets, public and private elements, source server,name ".
            "\n can change ownership",
        summary: 'Allows the system to set namespace data',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Namespace\EditPromotion::class)]
    public function edit_promotion() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }






    #[OA\Delete(
        path: '/api/v1/namespaces/destroy_namespace',
        operationId: 'core.namespaces.destroy_namespace',
        description: "User can destroy any namespace they own except their default namespace ",
        summary: 'The owner can destroy a namespace',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiEventMarker( Evt\Server\NamespaceDestroyed::class)]
    #[ApiTypeMarker( Root\Api\Namespace\Destroy::class)]
    public function destroy_namespace() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Post(
        path: '/api/v1/namespaces/create_namespace',
        operationId: 'core.namespaces.create_namespace',
        description: "user make new namespace. ".
        "\n can set new homesets, public and private elements, source server,name, user, other data ",
        summary: 'The user creates a new namespace with themself as the owner',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\NamespaceCreated::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_OWNER)]
    #[ApiTypeMarker( Root\Api\Namespace\Create::class)]
    public function create_namespace() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/namespaces/promote_namespace',
        operationId: 'core.namespaces.promote_namespace',
        description: "System make new namespaces and assign anyone as the owner. ".
        "\n can set new homesets, public and private elements, source server,name, user, other data ",
        summary: 'Allows the system to make a new namespace',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Namespace\Promote::class)]
    public function promote_namespace() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Delete(
        path: '/api/v1/namespaces/purge_namespace',
        operationId: 'core.namespaces.purge_namespace',
        description: "System can destroy any namespaces without events going off ",
        summary: 'Allows the system to destroy any namespace',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Namespace\Purge::class)]
    public function purge_namespace() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }






    #[OA\Post(
        path: '/api/v1/namespaces/add_admin',
        operationId: 'core.namespaces.add_admin',
        description: "Owner can add a new administrator (who will also be a member).Event goes to handle ",
        summary: 'Add a new admin to the namespace',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Element\NamespaceAdminAdding::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_OWNER)]
    #[ApiTypeMarker( Root\Api\Namespace\AddAdmin::class)]
    public function add_admin() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/namespaces/list_admins',
        operationId: 'core.namespaces.list_admins',
        description: "Any member can use this to get a full list of all the admins (includes owner). Can filter by handle or admin uuid or name",
        summary: 'Shows a list of all the admins from this namespace',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Namespace\ListAdmins::class)]
    public function list_admins() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Delete(
        path: '/api/v1/namespaces/remove_admin',
        operationId: 'core.namespaces.remove_admin',
        description: "Owner can remove administrator (who will still be a member).Event goes to handle ",
        summary: 'Remove admin privs from a member in the namespace',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Element\NamespaceAdminRemoving::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_OWNER)]
    #[ApiTypeMarker( Root\Api\Namespace\RemoveAdmin::class)]
    public function remove_admin() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }







    #[OA\Delete(
        path: '/api/v1/namespaces/purge_admin',
        operationId: 'core.namespaces.purge_admin',
        description: "System can remove any admin from any group without raising events (person is still member) ",
        summary: 'System can remove admins from namespaces',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Namespace\PurgeAdmin::class)]
    public function purge_admin() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/namespaces/promote_admin',
        operationId: 'core.namespaces.promote_admin',
        description: "System can add anyone to be admin in group without raising events ",
        summary: 'System can add admins to namespaces',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiTypeMarker( Root\Api\Namespace\PromoteAdmin::class)]
    public function promote_admin() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/namespaces/add_member',
        operationId: 'core.namespaces.add_member',
        description: "Admin can add any other namespace as a member. Event goes to handle",
        summary: 'Add a member to the namespace',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Element\NamespaceMemberAdding::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Namespace\AddMember::class)]
    public function add_member() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Delete(
        path: '/api/v1/namespaces/remove_member',
        operationId: 'core.namespaces.remove_member',
        description: "Admin can remove member who is not administrator. Event goes to handle ",
        summary: 'Remove member from the namespace',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Element\NamespaceMemberRemoving::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Namespace\RemoveMember::class)]
    public function remove_member() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Post(
        path: '/api/v1/namespaces/promote_member',
        operationId: 'core.namespaces.promote_member',
        description: "System can add any member from group without raising events ",
        summary: 'System can add members to namespaces',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Namespace\PromoteMember::class)]
    public function promote_member() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Delete(
        path: '/api/v1/namespaces/purge_member',
        operationId: 'core.namespaces.purge_member',
        description: "System can remove any member from any group without raising events ",
        summary: 'System can remove members from namespaces',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Namespace\PurgeMember::class)]
    public function purge_member() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Get(
        path: '/api/v1/namespaces/list_members',
        operationId: 'core.namespaces.list_members',
        description: "Any member can use this to get a full list of all the members. Can filter by handle or member uuid/name",
        summary: 'Shows a list of all the members from this namespace',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Namespace\ListMembers::class)]
    public function list_members() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/namespaces/add_handle',
        operationId: 'core.namespaces.add_handle',
        description: "Namespaces can be grouped, organized and controlled together",
        summary: 'Add element handle to a namespace',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\NamespaceHandleAdded::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::PATH_ADMIN)]
    #[ApiTypeMarker( Root\Api\Namespace\AddHandle::class)]
    public function add_handle() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Patch(
        path: '/api/v1/namespaces/remove_handle',
        operationId: 'core.namespaces.remove_handle',
        description: "Handles can be removed at any time, and left empty or new ones added",
        summary: 'Remove element handle from a namespaces',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\NamespaceHandleRemoved::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::PATH_ADMIN)]
    #[ApiTypeMarker( Root\Api\Namespace\RemoveHandle::class)]
    public function remove_handle() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


}
