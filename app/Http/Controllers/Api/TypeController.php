<?php

namespace App\Http\Controllers\Api;

use App\Annotations\Access\TypeOfAccessMarker;
use App\Annotations\ApiAccessMarker;
use App\Annotations\ApiEventMarker;
use App\Annotations\ApiTypeMarker;
use App\Data\ApiParams\Data\Elements\Params\CreateElementParamData;
use App\Data\ApiParams\Data\Elements\Params\SelectElementParamData;
use App\Data\ApiParams\Data\Elements\Responses\ElementList;
use App\Data\ApiParams\Data\Schedules\Schedule;
use App\Data\ApiParams\Data\Types\ElementTypeData;
use App\Data\ApiParams\Data\Types\Params\TypeParamData;
use App\Data\ApiParams\Data\Types\Params\TypeSearchParams;
use App\Data\ApiParams\Data\Types\Responses\ElementTypeList;
use App\Data\ApiParams\OpenApi\Common\Resources\HexbatchNamespace;
use App\Data\ApiParams\OpenApi\Common\Resources\HexbatchResource;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
use App\Models\ElementType;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Data\ThangData;
use Hexbatch\Thangs\Models\Thang;
use Hexbatch\Things\OpenApi\Things\ThingResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Response as CodeOf;


class TypeController extends Controller {


    /**
     * @throws \Exception
     */
    #[OA\Get(
        path: '/api/v1/{user_namespace}/types/{element_type}/show',
        operationId: 'core.types.show',
        description: "See information about a type if one is a member, admin or owner ",
        summary: 'Show information about a type',
        security: [['bearerAuth' => []]],
        tags: ['type'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Type info returned', content: new JsonContent(ref: ElementTypeData::class)),

            new OA\Response(    response: CodeOf::HTTP_FORBIDDEN, description: 'Not allowed'),
            new OA\Response(    response: CodeOf::HTTP_NOT_FOUND, description: 'A resource was not found')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Type\ShowType::class)]
    public function show_type(UserNamespace $namespace,ElementType $type) {
        Utilities::ignoreVar($namespace);
        $data_out = Root\Api\Type\ShowType::showType($type);
        return  response()->json($data_out,CodeOf::HTTP_OK);
    }



    #[OA\Get(
        path: '/api/v1/types/{element_type}/show_public',
        operationId: 'core.types.show_public',
        description: "Anyone can see public information including about and meta, name and current status ",
        summary: 'Show public data for a type',
        tags: ['type','public'],
        parameters: [

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::IS_PUBLIC)]
    #[ApiTypeMarker( Root\Api\Type\ShowPublic::class)]
    public function show_type_public() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    /**
     * @throws \Exception
     */
    #[OA\Get(
        path: '/api/v1/{user_namespace}/types/list_published',
        operationId: 'core.types.list_published',
        description: "Can see any published types where one is a member, admin or owner ",
        summary: 'List published types',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: TypeParamData::class)),
        tags: ['type'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),
        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Type info listeed', content: new JsonContent(ref: ElementTypeList::class)),

        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Type\ListPublished::class)]
    public function list_published(UserNamespace $namespace,Request $request) {
        $params = TypeSearchParams::fromRequest($request);
        $data_out = Root\Api\Type\ListPublished::listPublished(calling_namespace: $namespace,params: $params);
        return  response()->json($data_out,CodeOf::HTTP_OK);
    }


    /**
     * @throws \Exception
     */
    #[OA\Get(
        path: '/api/v1/{user_namespace}/types/list_suspended',
        operationId: 'core.types.list_suspended',
        description: "Can see any suspended types where one is a member, admin or owner ",
        summary: 'List suspended types',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: TypeParamData::class)),
        tags: ['type'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Type info listeed', content: new JsonContent(ref: ElementTypeList::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Type\ListSuspended::class)]
    public function list_suspended(UserNamespace $namespace,Request $request) {
        $params = TypeSearchParams::fromRequest($request);
        $data_out = Root\Api\Type\ListSuspended::listSuspended(calling_namespace: $namespace,params: $params);
        return  response()->json($data_out,CodeOf::HTTP_OK);
    }





    #[OA\Get(
        path: '/api/v1/types/list_all_suspended',
        operationId: 'core.types.list_all_suspended',
        description: "Public can see any suspended types ",
        summary: 'List all suspended types',
        tags: ['type','public'],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::IS_PUBLIC)]
    #[ApiTypeMarker( Root\Api\Type\ListAllSuspended::class)]
    public function list_all_suspended() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Get(
        path: '/api/v1/{user_namespace}/types/{element_type}/list_live',
        operationId: 'core.types.list_live',
        description: "Members can see all the currently applied live using this type",
        summary: 'Show live types made from this type',
        security: [['bearerAuth' => []]],
        tags: ['type','live'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Type\ListLive::class)]
    public function list_live() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    /**
     * @throws \Exception
     */
    #[OA\Get(
        path: '/api/v1/{user_namespace}/types/{element_type}/list_elements',
        operationId: 'core.types.list_elements',
        description: "Members can see all the elements created. Use the element show command to get information about element",
        summary: 'Show elements made from this type',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: SelectElementParamData::class)),
        tags: ['type','element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Listed elements of this type', content: new JsonContent(ref: ElementList::class)),

        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Type\ListElementsOfType::class)]
    public function list_elements(UserNamespace $namespace,Request $request,ElementType $type) {
        $params = SelectElementParamData::fromRequest($request);
        $params->type_ref = $type->ref_uuid;
        $data_out = Root\Api\Type\ListElementsOfType::listElements(params: $params, caller_namespace: $namespace);
        return  response()->json($data_out,CodeOf::HTTP_OK);
    }






    #[OA\Get(
        path: '/api/v1/{user_namespace}/types/{element_type}/list_descendants',
        operationId: 'core.types.list_descendants',
        description: "Members can see how this type is being inherited by other types.",
        summary: 'Show type inheritance',
        security: [['bearerAuth' => []]],
        tags: ['type'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Type\ListDescendants::class)]
    public function list_descendants() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Get(
        path: '/api/v1/{user_namespace}/types/{element_type}/list_attribute_descendants',
        operationId: 'core.types.list_attribute_descendants',
        description: "Members can see how this type's attributes are being used in other types.",
        summary: 'Show attribute inheritance',
        security: [['bearerAuth' => []]],
        tags: ['type','attribute'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Type\ListAttributeDescendants::class)]
    public function list_attribute_descendants() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }








    #[OA\Patch(
        path: '/api/v1/{user_namespace}/types/{element_type}/add_handle',
        operationId: 'core.types.add_handle',
        description: "Types can be grouped, organized and controlled together",
        summary: 'Add element handle to a type',
        security: [['bearerAuth' => []]],
        tags: ['type'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\TypeHandleAdded::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Type\AddHandle::class)]
    public function add_handle() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Patch(
        path: '/api/v1/{user_namespace}/types/{element_type}/remove_handle',
        operationId: 'core.types.remove_handle',
        description: "Handles can be removed at any time, and be empty or new ones added",
        summary: 'Remove element handle from a type',
        security: [['bearerAuth' => []]],
        tags: ['type'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\TypeHandleRemoved::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Type\RemoveHandle::class)]
    public function remove_handle() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }








    #[OA\Patch(
        path: '/api/v1/{user_namespace}/types/{element_type}/change_owner',
        operationId: 'core.types.change_owner',
        description: "The type owner can give the type to any other namespace",
        summary: 'Changes the type owner',
        security: [['bearerAuth' => []]],
        tags: ['type'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\TypeOwnerChanging::class)]
    #[ApiEventMarker( Evt\Server\TypeOwnerChanged::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_OWNER)]
    #[ApiTypeMarker( Root\Api\Type\ChangeOwner::class)]
    public function change_owner() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Patch(
        path: '/api/v1/{user_namespace}/types/{element_type}/promote_owner/{target_namespace}',
        operationId: 'core.types.promote_owner',
        description: "Type owners can be changed by the system without events or permission",
        summary: 'System can change the type owner',
        security: [['bearerAuth' => []]],
        tags: ['type'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'target_namespace', description: "The new namespace",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Type\PromoteOwner::class)]
    public function promote_owner(UserNamespace $namespace,ElementType $element_type,UserNamespace $target_namespace) {
        Utilities::ignoreVar($namespace,$element_type,$target_namespace);
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Delete(
        path: '/api/v1/{user_namespace}/types/{element_type}/destroy_type',
        operationId: 'core.types.destroy_type',
        description: "The type owner destroy the type, inheritied types and event on this type can block",
        summary: 'Changes the type owner',
        security: [['bearerAuth' => []]],
        tags: ['type'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\TypeDeleted::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_OWNER)]
    #[ApiTypeMarker( Root\Api\Type\DestroyType::class)]
    public function destroy_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Delete(
        path: '/api/v1/{user_namespace}/types/{element_type}/purge',
        operationId: 'core.types.purge',
        description: "System can delete types, and all their elements and sets, without events or permission",
        summary: 'System can delete types',
        security: [['bearerAuth' => []]],
        tags: ['type','element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Type\Purge::class)]
    public function purge_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Post(
        path: '/api/v1/{user_namespace}/types/{element_type}/fire_event',
        operationId: 'core.types.fire_event',
        description: "Custom events can be fired, their scope depends on what they inherit, smallest scope wins if multiple ancestors of mixed scope",
        summary: 'Fires a custom event',
        security: [['bearerAuth' => []]],
        tags: ['type','event'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\CustomEventFired::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Type\FireEvent::class)]
    public function fire_event() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    /**
     * @throws \Exception
     * @throws \Throwable
     */
    #[OA\Patch(
        path: '/api/v1/{user_namespace}/types/{element_type}/publish',
        operationId: 'core.types.publish',
        description: "Type admins do unpublished design and mark it as ready for use. Events from inherited types and attributes can block",
        summary: 'Publishes a design',
        security: [['bearerAuth' => []]],
        tags: ['type'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Type Published', content: new JsonContent(ref: ElementTypeData::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Processing|waiting', content: new JsonContent(ref: ThangData::class)),

            new OA\Response(    response: CodeOf::HTTP_UNPROCESSABLE_ENTITY, description: 'If abstract attributes'),
            new OA\Response(    response: CodeOf::HTTP_FORBIDDEN, description: 'Not allowed'),
            new OA\Response(    response: CodeOf::HTTP_NOT_FOUND, description: 'A resource was not found')
        ]
    )]
    #[ApiEventMarker( Evt\Type\TypePublishing::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Type\Publish::class)]
    public function publish_type(UserNamespace $namespace,ElementType $type) {
        $data = Root\Api\Type\Publish::doPublish(
            calling_namespace: $namespace,given_type: $type,do_permission_check: true ,tags: ['api-top']);

        if ($data instanceof Thang) {
            $http_code = CodeOf::HTTP_ACCEPTED;
            $data_out = $data;
        }
        else {
            $http_code = CodeOf::HTTP_CREATED;
            $data_out = Schedule::validateAndCreate($data);
        }
        return  response()->json($data_out,$http_code);
    }


    /**
     * @throws \Throwable
     */
    #[OA\Patch(
        path: '/api/v1/{user_namespace}/types/{element_type}/promote_publish',
        operationId: 'core.types.promote_publish',
        description: "System can publish any design. This overrules any rules denying it (those events do not fire) ",
        summary: 'System publishes a design',
        security: [['bearerAuth' => []]],
        tags: ['type'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Type Published', content: new JsonContent(ref: ElementTypeData::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Processing|waiting', content: new JsonContent(ref: ThangData::class)),

            new OA\Response(    response: CodeOf::HTTP_NOT_FOUND, description: 'A resource was not found')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Type\Publish::class)]
    public function publish_type_promote(UserNamespace $namespace,ElementType $type) {
        $data = Root\Api\Type\Publish::doPublish(
            calling_namespace: $namespace,given_type: $type,do_permission_check: false ,tags: ['api-top']);

        if ($data instanceof Thang) {
            $http_code = CodeOf::HTTP_ACCEPTED;
            $data_out = $data;
        }
        else {
            $http_code = CodeOf::HTTP_CREATED;
            $data_out = Schedule::validateAndCreate($data);
        }
        return  response()->json($data_out,$http_code);
    }


    #[OA\Patch(
        path: '/api/v1/{user_namespace}/types/{element_type}/suspend',
        operationId: 'core.types.suspend',
        description: "System can suspend a type. New elements cannot be created, but existing ones are left alone. ".
                "\n Suspended types do not listen to events ".
                "\n Can be blocked by events (system only listeners block, others listen to after the fact)",
        summary: 'Suspends a design',
        security: [['bearerAuth' => []]],
        tags: ['type'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\TypeSuspended::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Type\Suspend::class)]
    public function suspend_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Patch(
        path: '/api/v1/{user_namespace}/types/{element_type}/retire',
        operationId: 'core.types.retire',
        description: "Type admins retire a type. Events from inherited types and attributes can block. ".
                    "\nUnpublished types using this after acceptance can still use it",
        summary: 'Retires a design',
        security: [['bearerAuth' => []]],
        tags: ['type'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\TypeRetired::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Type\Retire::class)]
    public function retire_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    /**
     * @throws \Exception
     * @throws \Throwable
     */
    #[OA\Post(
        path: '/api/v1/{user_namespace}/types/{element_type}/create_element',
        operationId: 'core.types.create_element',
        description: "Type admin can create one or more elements going to one or more namespaces. The namespace can reject. The inherited types can reject",
        summary: 'Creates one or more new elements from a type',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: CreateElementParamData::class)),
        tags: ['type','element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Elements created', content: new JsonContent(ref: ElementList::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Processing|waiting', content: new JsonContent(ref: ThangData::class)),

            new OA\Response(    response: CodeOf::HTTP_UNPROCESSABLE_ENTITY, description: 'There was an issue') ,
            new OA\Response(    response: CodeOf::HTTP_FORBIDDEN, description: 'Not allowed'),
            new OA\Response(    response: CodeOf::HTTP_NOT_FOUND, description: 'A resource was not found')
        ]
    )]
    #[ApiEventMarker( Evt\Type\ElementOwnerChange::class)]
    #[ApiEventMarker( Evt\Type\ElementRecieved::class)]
    #[ApiEventMarker( Evt\Type\ElementCreation::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]

    #[ApiTypeMarker( Root\Api\Type\CreateElement::class)]
    public function create_element(UserNamespace $namespace,ElementType $type,Request $request) {

        $params = CreateElementParamData::fromRequest($request);
        $data_out = Root\Api\Type\CreateElement::doElementCreation(
            calling_namespace: $namespace,given_type:$type,is_system: false, params: $params,tags: ['api-top']);

        if ($data_out instanceof Thang) {
            $http_code = CodeOf::HTTP_OK;
            $data_out = ThangData::from($data_out);
        }
        else {
            $http_code = CodeOf::HTTP_CREATED;
        }
        return  response()->json($data_out,$http_code);
    }


    #[OA\Post(
        path: '/api/v1/{user_namespace}/types/{element_type}/promote_element',
        operationId: 'core.types.promote_element',
        description: "System can make elements out of any types, not needing permissions. No events created when doing this",
        summary: 'Creates one or more elements',
        security: [['bearerAuth' => []]],
        tags: ['type','element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Type\PromoteElement::class)]
    public function promote_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

}
