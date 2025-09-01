<?php

namespace App\Http\Controllers\Api;


use App\Annotations\Access\TypeOfAccessMarker;
use App\Annotations\ApiAccessMarker;
use App\Annotations\ApiEventMarker;
use App\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;
use App\OpenApi\Common\Resources\HexbatchNamespace;
use App\OpenApi\Common\Resources\HexbatchResource;
use App\Sys\Res\Types\Stk\Root;
use App\Sys\Res\Types\Stk\Root\Evt;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class NamespaceController extends Controller {
    #[OA\Get(
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/show',
        operationId: 'core.namespaces.show',
        description: "Namespace members can run this to see the owner, the name, the count of admins, members, types, elements ".
        "\n Will show a list of the first admins (not a complete list)",
        summary: 'Shows a summary of the namespace',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),
        ],
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
        path: '/api/v1/namespaces/public',
        operationId: 'core.namespaces.show_public',
        description: "Anyone can run this to see some info about the namespace. Will only show public data",
        summary: 'Shows a summary of the namespace',
        tags: ['namespace','public'],
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
        path: '/api/v1/{user_namespace}/namespaces/list_namespaces',
        operationId: 'core.namespaces.list',
        description: "Will show owned, admin and member status of all namespaces this caller is part of. Can filter by handle or namespace name",
        summary: 'Shows all the namespaces this caller is part of',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),
        ],
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
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/edit_promotion',
        operationId: 'core.namespaces.edit_promotion',
        description: "System can set data in namespaces without events going off. ".
            "\n can set new homesets, public and private elements, source server,name ".
            "\n can change ownership",
        summary: 'Allows the system to set namespace data',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),
        ],
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
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/destroy',
        operationId: 'core.namespaces.destroy',
        description: "User can destroy any namespace they own except their default namespace ",
        summary: 'The owner can destroy a namespace',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),
        ],
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
        path: '/api/v1/{user_namespace}/namespaces/create',
        operationId: 'core.namespaces.create',
        description: "user make new namespace. ".
        "\n can set new homesets, public and private elements, source server,name, user, other data ",
        summary: 'The user creates a new namespace with themself as the owner',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),
        ],
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
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/transfer_owner',
        operationId: 'core.namespaces.transfer_owner',
        description: "The selected namespaces are given to another user as long as they were processed in the starting transfer step as a safety check ".
        "\n The event is sent after the fact. If this is a transfer of a default ns, then a new default ns is made for that user giving it up ",
        summary: 'The user gives the namespace(s) to another user',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),
        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\NamespaceTransfered::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_OWNER)]
    #[ApiTypeMarker( Root\Api\Namespace\TransferOwner::class)]
    public function transfer_owner() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/start_transfer',
        operationId: 'core.namespaces.start_transfer',
        description: "The selected namespaces are marked as allowed for transfer. Event can stop this. Not transferred yet. ",
        summary: 'The user gives permission for the transfer of the namespace(s)',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),
        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\NamespaceTransfered::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_OWNER)]
    #[ApiTypeMarker( Root\Api\Namespace\StartTransfer::class)]
    public function start_transfer() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/{user_namespace}/namespaces/promote',
        operationId: 'core.namespaces.promote',
        description: "System make new namespaces and assign anyone as the owner. ".
        "\n can set new homesets, public and private elements, source server,name, user, other data ",
        summary: 'Allows the system to make a new namespace',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),
        ],
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
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/purge',
        operationId: 'core.namespaces.purge',
        description: "System can destroy any namespaces without events going off ",
        summary: 'Allows the system to destroy any namespace',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),
        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Namespace\Purge::class)]
    public function purge_namespace() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





//HexbatchResource::class
    #[OA\Post(
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/add_admin',
        operationId: 'core.namespaces.add_admin',
        description: "Owner can add a new administrator (who will also be a member).Event goes to handle ",
        summary: 'Add a new admin to the namespace',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),
        ],
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
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/list_admins',
        operationId: 'core.namespaces.list_admins',
        description: "Any member can use this to get a full list of all the admins (includes owner). Can filter by handle or admin uuid or name",
        summary: 'Shows a list of all the admins from this namespace',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),
        ],
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
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/remove_admin',
        operationId: 'core.namespaces.remove_admin',
        description: "Owner can remove administrator (who will still be a member).Event goes to handle ",
        summary: 'Remove admin privs from a member in the namespace',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),
        ],
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
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/purge_admin',
        operationId: 'core.namespaces.purge_admin',
        description: "System can remove any admin from any group without raising events (person is still member) ",
        summary: 'System can remove admins from namespaces',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),
        ],
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
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/promote_admin',
        operationId: 'core.namespaces.promote_admin',
        description: "System can add anyone to be admin in group without raising events ",
        summary: 'System can add admins to namespaces',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),
        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiTypeMarker( Root\Api\Namespace\PromoteAdmin::class)]
    public function promote_admin() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/add_member',
        operationId: 'core.namespaces.add_member',
        description: "Admin can add any other namespace as a member. Event goes to handle",
        summary: 'Add one or more members to the namespace',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),
        ],
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
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/remove_member',
        operationId: 'core.namespaces.remove_member',
        description: "Admin can remove member who is not administrator. Event goes to handle ",
        summary: 'Remove members from the namespace',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),
        ],
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
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/promote_member',
        operationId: 'core.namespaces.promote_member',
        description: "System can add any member from group without raising events ",
        summary: 'System can add members to namespaces',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),
        ],
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
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/purge_member',
        operationId: 'core.namespaces.purge_member',
        description: "System can remove any member from any group without raising events ",
        summary: 'System can remove members from namespaces',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),
        ],
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
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/list_members',
        operationId: 'core.namespaces.list_members',
        description: "Any member can use this to get a full list of all the members. Can filter by handle or member uuid/name",
        summary: 'Shows a list of all the members from this namespace',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),
        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Namespace\ListMembers::class)]
    public function list_members() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Patch(
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/add_handle',
        operationId: 'core.namespaces.add_handle',
        description: "Namespaces can be grouped, organized and controlled together",
        summary: 'Add element handle to a namespace',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),
        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\NamespaceHandleAdded::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_OWNER)]
    #[ApiTypeMarker( Root\Api\Namespace\AddHandle::class)]
    public function add_handle() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Patch(
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/remove_handle',
        operationId: 'core.namespaces.remove_handle',
        description: "Handles can be removed at any time, and left empty or new ones added",
        summary: 'Remove element handle from a namespaces',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),
        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\NamespaceHandleRemoved::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_OWNER)]
    #[ApiTypeMarker( Root\Api\Namespace\RemoveHandle::class)]
    public function remove_handle() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


}
