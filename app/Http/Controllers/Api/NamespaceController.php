<?php

namespace App\Http\Controllers\Api;


use App\Annotations\Access\TypeOfAccessMarker;
use App\Annotations\ApiAccessMarker;
use App\Annotations\ApiEventMarker;
use App\Annotations\ApiTypeMarker;
use App\Data\ApiParams\Data\Elements\Responses\ElementList;
use App\Data\ApiParams\Data\Namespaces\Params\DeleteNamespacesParamData;
use App\Data\ApiParams\Data\Namespaces\Params\ListMembersParamData;
use App\Data\ApiParams\Data\Namespaces\Params\ListNamespacesParamData;
use App\Data\ApiParams\Data\Namespaces\Params\NamespaceParamData;
use App\Data\ApiParams\Data\Namespaces\Responses\NamespaceMemberListData;
use App\Data\ApiParams\Data\Namespaces\Responses\UserNamespaceListData;
use App\Data\ApiParams\Data\Namespaces\UserNamespaceData;
use App\Data\ApiParams\OpenApi\Common\Resources\HexbatchNamespace;
use App\Data\ApiParams\OpenApi\Common\Resources\HexbatchResource;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Data\ThangData;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
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
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),
        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_OK, description: 'Lists ns info, if caller admin of name, will list private',content: new JsonContent(ref: UserNamespaceData::class)),
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Namespace\Show::class)]
    public function show_namespace(UserNamespace $namespace,UserNamespace $target) {
        $out = Root\Api\Namespace\Show::showNamespace(caller: $namespace,target:$target);
        return response()->json($out, CodeOf::HTTP_OK);
    }





    #[OA\Get(
        path: '/api/v1/{user_namespace}/namespaces/list',
        operationId: 'core.namespaces.list',
        description: "Will list namespaces searched",
        summary: 'Shows cursored list of namespaces filtered by search params',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: ListNamespacesParamData::class)),
        tags: ['namespace'],

        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),
        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Location results returned', content: new JsonContent(ref: UserNamespaceListData::class)),
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    #[ApiTypeMarker( Root\Api\Namespace\ListNamespaces::class)]
    public function list_namespaces(UserNamespace $namespace,Request $request) {
        $params = ListNamespacesParamData::fromRequest($request);
        $data_out = Root\Api\Namespace\ListNamespaces::listNamespaces(params: $params,caller_namespace: $namespace);
        return  response()->json($data_out,CodeOf::HTTP_OK);
    }


    #[OA\Get(
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/list_members',
        operationId: 'core.namespaces.list_members',
        description: "Any member can use this to get a full list of all the members. Can filter by handle or member uuid/name",
        summary: 'Shows a list of all the members from this namespace',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: ListMembersParamData::class)),
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),
        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Location results returned', content: new JsonContent(ref: NamespaceMemberListData::class)),
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Namespace\ListMembers::class)]
    public function list_members(UserNamespace $namespace,UserNamespace $target_namespace,Request $request) {
        $params = ListMembersParamData::fromRequest($request);
        $data_out = Root\Api\Namespace\ListMembers::listMembers(params: $params,caller_namespace: $namespace,target_namespace: $target_namespace);
        return  response()->json($data_out,CodeOf::HTTP_OK);
    }


    /**
     * @throws \Throwable
     */
    #[OA\Post(
        path: '/api/v1/{user_namespace}/namespaces/create',
        operationId: 'core.namespaces.create',
        description: "User can make additional namespace. ".
        "\n can set new homesets, public and private elements, source server,name, user, other data ",
        summary: 'The user creates a new namespace with themself as the owner',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: NamespaceParamData::class)),
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),
        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Namespace created', content: new JsonContent(ref: ElementList::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Processing|waiting', content: new JsonContent(ref: ThangData::class)),
            new OA\Response(    response: CodeOf::HTTP_FORBIDDEN, description: 'Not allowed'),
        ]
    )]
    #[ApiEventMarker( Evt\Server\NamespaceCreated::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_OWNER)]
    #[ApiTypeMarker( Root\Api\Namespace\Create::class)]
    public function create_namespace(UserNamespace $namespace,Request $request) {
        $params = NamespaceParamData::fromRequest($request);
        $data_out =  Root\Api\Namespace\Create::doNamespaceCreate(
            params: $params,   calling_namespace: $namespace, given_user: null , is_system: false, tags: ['api-top']);

        if ($data_out instanceof Thang) {
            $http_code = CodeOf::HTTP_OK;
            $data_out = ThangData::from($data_out);
        }
        else {
            $http_code = CodeOf::HTTP_CREATED;
        }
        return  response()->json($data_out,$http_code);
    }


    /**
     * @throws \Throwable
     */
    #[OA\Post(
        path: '/api/v1/{user_namespace}/namespaces/promote/{user}',
        operationId: 'core.namespaces.promote',
        description: "System make new namespaces and assign anyone as the owner. ".
        "\n can set new homesets, public and private elements, source server,name, user, other data ",
        summary: 'Allows the system to make a new namespace',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: NamespaceParamData::class)),
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),
        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Namespace created', content: new JsonContent(ref: ElementList::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Processing|waiting', content: new JsonContent(ref: ThangData::class)),
            new OA\Response(    response: CodeOf::HTTP_FORBIDDEN, description: 'Not allowed'),
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Namespace\Promote::class)]
    public function promote_namespace(UserNamespace $namespace,User $user,Request $request) {
        $params = NamespaceParamData::fromRequest($request);
        $data_out =  Root\Api\Namespace\Create::doNamespaceCreate(
            params: $params,   calling_namespace: $namespace, given_user: $user , is_system: false, tags: ['api-top']);

        if ($data_out instanceof Thang) {
            $http_code = CodeOf::HTTP_OK;
            $data_out = ThangData::from($data_out);
        }
        else {
            $http_code = CodeOf::HTTP_CREATED;
        }
        return  response()->json($data_out,$http_code);
    }


    /**
     * @throws \Throwable
     */
    #[OA\Post(
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/start_deletion',
        operationId: 'core.namespaces.start_deletion',
        description: "The selected namespaces is marked as deleted.  Not deleted here, must call delete. ",
        summary: 'The user gives permission for the transfer of the namespace(s)',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DeleteNamespacesParamData::class)),
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),
        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Deletion marked', content: new JsonContent(ref: DeleteNamespacesParamData::class)),
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_OWNER)]
    #[ApiTypeMarker( Root\Api\Namespace\StartDeletion::class)]
    public function start_deletion(UserNamespace $namespace,UserNamespace $target_namespace,Request $request) {
        $params = DeleteNamespacesParamData::fromRequest($request);
        $data_out = Root\Api\Namespace\StartDeletion::doStartDeletion(params: $params,calling_namespace: $namespace,target_namespace: $target_namespace);
        return  response()->json($data_out,CodeOf::HTTP_OK);
    }


    /**
     * @throws \Throwable
     */
    #[OA\Delete(
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/destroy',
        operationId: 'core.namespaces.destroy',
        description: "User can destroy any namespace they own except their default namespace ",
        summary: 'The owner can destroy a namespace',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DeleteNamespacesParamData::class)),
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),
        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Namespace deleted', content: new JsonContent(ref: UserNamespaceData::class)),

            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Processing|waiting', content: new JsonContent(ref: ThangData::class)),

            new OA\Response(    response: CodeOf::HTTP_FORBIDDEN, description: 'Not allowed'),
            new OA\Response(    response: CodeOf::HTTP_NOT_FOUND, description: 'Set was not found')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiEventMarker( Evt\Server\NamespaceDestroyed::class)]
    #[ApiTypeMarker( Root\Api\Namespace\Destroy::class)]
    public function destroy_namespace(UserNamespace $namespace,UserNamespace $target_namespace,Request $request) {
        $params = DeleteNamespacesParamData::fromRequest($request);
        $data_out = Root\Api\Namespace\Destroy::doDeletion(params: $params,calling_namespace: $namespace,target_namespace: $target_namespace);
        if ($data_out instanceof Thang) {
            $data_out = ThangData::from($data_out);
            $http_code = CodeOf::HTTP_OK;
        }
        else {
            $http_code = CodeOf::HTTP_ACCEPTED;
        }
        return  response()->json($data_out,$http_code);
    }


    /**
     * @throws \Throwable
     */
    #[OA\Delete(
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/purge',
        operationId: 'core.namespaces.purge',
        description: "System can destroy any non default namespaces ",
        summary: 'Allows the system to destroy any namespace',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DeleteNamespacesParamData::class)),
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),
        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Namespace deleted', content: new JsonContent(ref: UserNamespaceData::class)),

            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Processing|waiting', content: new JsonContent(ref: ThangData::class)),

            new OA\Response(    response: CodeOf::HTTP_FORBIDDEN, description: 'Not allowed'),
            new OA\Response(    response: CodeOf::HTTP_NOT_FOUND, description: 'Set was not found')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Namespace\Purge::class)]
    public function purge_namespace(UserNamespace $namespace,UserNamespace $target_namespace,Request $request) {
        $params = DeleteNamespacesParamData::fromRequest($request);
        $data_out = Root\Api\Namespace\Destroy::doDeletion(params: $params,calling_namespace: $namespace,target_namespace: $target_namespace,do_permission_check: false);
        if ($data_out instanceof Thang) {
            $data_out = ThangData::from($data_out);
            $http_code = CodeOf::HTTP_OK;
        }
        else {
            $http_code = CodeOf::HTTP_ACCEPTED;
        }
        return  response()->json($data_out,$http_code);
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
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),
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
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),
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
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),
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









    #[OA\Delete(
        path: '/api/v1/{user_namespace}/namespaces/{target_namespace}/remove_admin',
        operationId: 'core.namespaces.remove_admin',
        description: "Owner can remove administrator (who will still be a member).Event goes to handle ",
        summary: 'Remove admin privs from a member in the namespace',
        security: [['bearerAuth' => []]],
        tags: ['namespace'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),
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
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),
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
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),
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
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),
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
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),
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
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),
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
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'target_namespace', description: "The namespace this acts on",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),
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


}
