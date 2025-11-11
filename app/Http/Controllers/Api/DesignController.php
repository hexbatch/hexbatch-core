<?php

namespace App\Http\Controllers\Api;


use App\Annotations\Access\TypeOfAccessMarker;
use App\Annotations\ApiAccessMarker;
use App\Annotations\ApiEventMarker;
use App\Annotations\ApiTypeMarker;
use App\Data\ApiParams\Data\Schedules\Params\ScheduleSearchParams;
use App\Data\ApiParams\Data\Schedules\Responses\ScheduleList;
use App\Data\ApiParams\Data\Schedules\Schedule;
use App\Data\ApiParams\OpenApi\Common\Resources\HexbatchAttribute;
use App\Data\ApiParams\OpenApi\Common\Resources\HexbatchNamespace;
use App\Data\ApiParams\OpenApi\Common\Resources\HexbatchResource;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\ElementType;
use App\Models\LocationBound;
use App\Models\TimeBound;
use App\OpenApi\ApiResults\Attribute\ApiAttributeCollectionResponse;
use App\OpenApi\ApiResults\Attribute\ApiAttributeResponse;
use App\OpenApi\ApiResults\Bounds\ApiLocationCollectionResponse;
use App\OpenApi\ApiResults\Bounds\ApiLocationResponse;
use App\OpenApi\ApiResults\Type\ApiTypeCollectionResponse;
use App\OpenApi\ApiResults\Type\ApiTypeResponse;
use App\OpenApi\Params\Actioning\Design\DesignAttributeDestroyParams;
use App\OpenApi\Params\Actioning\Design\DesignAttributeParams;
use App\OpenApi\Params\Actioning\Design\DesignLocationParams;
use App\OpenApi\Params\Actioning\Design\DesignOwnershipParams;
use App\OpenApi\Params\Actioning\Design\DesignParams;
use App\OpenApi\Params\Actioning\Design\DesignParentParams;
use App\OpenApi\Params\Actioning\Type\TypeParams;
use App\OpenApi\Params\Listing\Design\ListAttributeParams;
use App\OpenApi\Params\Listing\Design\ListDesignParams;
use App\OpenApi\Params\Listing\Design\ListLocationParams;
use App\OpenApi\Params\Listing\Design\ShowAttributeParams;
use App\OpenApi\Params\Listing\Design\ShowDesignParams;
use App\OpenApi\Results\Callbacks\HexbatchCallbackCollectionResponse;
use App\Sys\Res\Types\Stk\Root;
use App\Sys\Res\Types\Stk\Root\Api;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Models\Thang;
use Hexbatch\Things\OpenApi\Things\ThingResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class DesignController extends Controller {


    /**
     * @throws \Exception
     */
    #[ApiTypeMarker( Root\Api\Design\ChangeOwner::class)]
    #[ApiEventMarker( Evt\Server\TypeOwnerChanging::class)]
    #[ApiEventMarker( Evt\Server\TypeOwnerChanged::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_OWNER)]
    #[OA\Post(
        path: '/api/v1/{user_namespace}/design/{element_type}/change_owner',
        operationId: 'core.design.change_owner',
        description: "The owner ns can transfer this unpublished design to be owned by another namespace.".
                    " Owners of subtypes can deny this change by listening to the type_owner_change event raised by this action",
        summary: 'Changes the ownership of a type before its published.  ',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DesignOwnershipParams::class)),
        tags: ['design'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Ownership changed', content: new JsonContent(ref: ApiTypeResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    public function change_design_owner(Request $request,ElementType $type) {
        $params = new DesignOwnershipParams(type: $type);
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\ChangeOwner(params: $params, is_async: true, tags: ['api-top']);
        $api->createThingTree(tags: ['change-owner']);

        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[ApiTypeMarker( Root\Api\Design\PromoteOwner::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[OA\Post(
        path: '/api/v1/{user_namespace}/design/{element_type}/promote_owner',
        operationId: 'core.design.promote_owner',
        description: 'The system can transfer this design to be managed by another namespace. No events are raised that can deny the change.',
        summary: 'Changes the ownership of a type before its published. ',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DesignParams::class)),
        tags: ['design'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Ownership changed', content: new JsonContent(ref: ApiTypeResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    public function promote_design_owner(Request $request,ElementType $type) {
        $params = new DesignOwnershipParams(type: $type);
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\PromoteOwner(params: $params, is_async: true, tags: ['api-top']);
        $api->createThingTree(tags: ['promote-owner']);

        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[ApiTypeMarker( Root\Api\Design\Purge::class)]
    #[OA\Delete(
        path: '/api/v1/{user_namespace}/design/{element_type}/purge',
        operationId: 'core.design.purge',
        description: 'The system can delete any design, owned by anyone, before publishing, without raising any events',
        summary: 'Purges an unpublished type ',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: TypeParams::class)),
        tags: ['design'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Design purged', content: new JsonContent(ref: ApiTypeResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiTypeMarker( Root\Api\Design\Purge::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    public function purge_design(Request $request,ElementType $type) {
        $params = new TypeParams(given_type: $type);
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\Purge(params: $params, is_async: true, tags: ['api-top']);
        $api->createThingTree(tags: ['purge-design']);

        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[ApiTypeMarker( Root\Api\Design\Destroy::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_OWNER)]
    #[OA\Delete(
        path: '/api/v1/{user_namespace}/design/{element_type}/destroy',
        operationId: 'core.design.destroy',
        description: 'A namespace can delete a new design, before publishing, without raising any events',
        summary: 'Deletes an unpublished type ',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: TypeParams::class)),
        tags: ['design'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Design destroyed', content: new JsonContent(ref: ApiTypeResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    public function destroy_design(Request $request,ElementType $type) {
        $params = new TypeParams(given_type: $type);
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\Destroy(params: $params, is_async: true, tags: ['api-top']);
        $api->createThingTree(tags: ['destroy-design']);

        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[ApiTypeMarker( Root\Api\Design\Create::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_OWNER)]
    #[OA\Post(
        path: '/api/v1/{user_namespace}/design/create',
        operationId: 'core.design.create',
        description: 'A namespace can make a new design, they are the owner. No events are raised',
        summary: 'Makes a new design type ',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DesignParams::class)),
        tags: ['design'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),
        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Type created', content: new JsonContent(ref: ApiTypeResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    public function create_design(Request $request) {
        $params = new DesignParams(namespace: Utilities::getCurrentOrUserNamespace());
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\Create(params: $params, is_async: true, tags: ['api-top']);
        $api->createThingTree(tags: ['create-design']);

        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[ApiTypeMarker( Root\Api\Design\Edit::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[OA\Patch(
        path: '/api/v1/{user_namespace}/design/{element_type}/edit',
        operationId: 'core.design.edit',
        description: "The owner or their admin group can change, before publishing, the name of the type, final status and access level ".
                        "\nNo events are raised",
        summary: 'Edits the name, final type, access ',
        security: [['bearerAuth' => []]],
        tags: ['design'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Type edited', content: new JsonContent(ref: ApiTypeResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    public function edit_design(Request $request,ElementType $type) {
        $params = new DesignParams(edit_type: $type, namespace: Utilities::getCurrentOrUserNamespace());
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\Edit(params: $params, is_async: true, tags: ['api-top']);
        $api->createThingTree(tags: ['edit-design']);

        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[OA\Get(
        path: '/api/v1/{user_namespace}/design/{element_type}/show',
        operationId: 'core.design.show',
        description: "See details about a type in any state. Lists the attributes, event listeners, live requirements and live rules ".
        "\nIf the design type is set to public access, then any namespace can use this to see the information. Otherwise only the members of the owner namepace can ".
        "\nTo see more detail about any one of these, use the show api for that reference",
        summary: 'Shows information about a type ',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: ShowDesignParams::class)),
        tags: ['design'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Type info returned', content: new JsonContent(ref: ApiTypeResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiTypeMarker( Root\Api\Design\ShowDesign::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    public function show_design(Request $request,ElementType $type) {
        $params = new ShowDesignParams(given_type: $type);
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\ShowDesign(params: $params, is_async: false, tags: ['api-top']);
        $api->createThingTree(tags: ['show-design']);

        $data_out = $api->getDataSnapshot();
        return  response()->json(['response'=>$data_out],$api->getCode());
    }


    /**
     * @throws \Exception
     */
    #[OA\Get(
        path: '/api/v1/{user_namespace}/design/list',
        operationId: 'core.design.list',
        description: "See a list of all the designs this namespace either owns or has admin rights, is a member. Once a type is published, it is not seen here".
                "\nCan see the name, uuid, the status and how many attributes, listeners, requirements and rules there are",
        summary: 'Lists all the designed owned or managed  ',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: ListDesignParams::class)),
        tags: ['design'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) )

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Type info listeed', content: new JsonContent(ref: ApiTypeCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiTypeMarker( Root\Api\Design\ListDesigns::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    public function list_designs(Request $request) {
        $params = new ListDesignParams();
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\ListDesigns(params: $params, is_async: false, tags: ['api-top']);
        $api->createThingTree(tags: ['list-designs']);

        $data_out = $api->getDataSnapshot();
        return  response()->json(['response'=>$data_out],$api->getCode());
    }


    /**
     * @throws \Exception
     */
    #[OA\Get(
        path: '/api/v1/{user_namespace}/design/{element_type}/attribute/{attribute}/show',
        operationId: 'core.design.show_attribute',
        description: "See information about an attribute. Will list the settings and bounds, will show parent, and status ".
                    "\nShows stats about its descendants".
                    "\nif the type is marked as public, and the attribute is marked as public then any namespace can use this, ".
                    " \notherwise the members of the owning namesapce can ",
        summary: 'Information about a single attribute on a type  ',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: ShowAttributeParams::class)),
        tags: ['design','attribute'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'attribute', description: "The attribute",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchAttribute::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Attribute info returned', content: new JsonContent(ref: ApiAttributeResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiTypeMarker( Root\Api\Design\ShowAttribute::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    public function show_attribute(Request $request,Attribute $attribute) {
        $params = new ShowAttributeParams(given_attribute: $attribute);
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\ShowAttribute(params: $params, is_async: false, tags: ['api-top']);
        $api->createThingTree(tags: ['show-attribute']);

        $data_out = $api->getDataSnapshot();
        return  response()->json(['response'=>$data_out],$api->getCode());
    }


    /**
     * @throws \Exception
     */
    #[OA\Get(
        path: '/api/v1/{user_namespace}/design/list_attributes',
        operationId: 'core.design.list_attributes',
        description: "See a list of attributes in namespaces that one belongs to",
        summary: 'Lists attributes with optional search',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: ListAttributeParams::class)),
        tags: ['design','attribute'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),
        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Attribute info returned', content: new JsonContent(ref: ApiAttributeCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Design\ListAttributes::class)]
    public function list_attributes(Request $request) {
        $params = new ListAttributeParams();
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\ListAttributes(params: $params, is_async: false, tags: ['api-top']);
        $api->createThingTree(tags: ['list-attributes']);

        $data_out = $api->getDataSnapshot();
        return  response()->json(['response'=>$data_out],$api->getCode());
    }


    /**
     * @throws \Exception
     */
    #[OA\Delete(
        path: '/api/v1/{user_namespace}/design/{element_type}/attribute/{attribute}/destroy',
        operationId: 'core.design.destroy_attribute',
        description: "Destroys an attribute. Attributes cannot be deleted after a type is published ".
        "\nRemoving design attributes does not generate any event",
        summary: 'Delete an attribute',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DesignAttributeDestroyParams::class)),
        tags: ['design','attribute'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'attribute', description: "The attribute",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchAttribute::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Attribute created', content: new JsonContent(ref: ApiAttributeResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiTypeMarker( Root\Api\Design\DestroyAttribute::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    public function destroy_attribute(Request $request,Attribute $attribute) {
        $params = new DesignAttributeDestroyParams(given_attribute: $attribute,namespace: Utilities::getCurrentOrUserNamespace());
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\DestroyAttribute(params: $params, is_async: true, tags: ['api-top']);
        $api->createThingTree(tags: ['destroy-attribute']);

        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }




    /**
     * @throws \Exception
     */
    #[ApiTypeMarker( Root\Api\Design\CreateAttribute::class)]
    #[ApiEventMarker( Evt\Server\DesignPending::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_OWNER)]
    #[OA\Post(
        path: '/api/v1/{user_namespace}/design/{element_type}/create_attribute',
        operationId: 'core.design.create_attribute',
        description: "Namespace admin group can create a new attribute using any parent chain for any design owned by anyone ".
        "\nBut the design can only be published if the inheritance chain does not block this using the design_pending event",
        summary: 'Creates a new attribute on a design',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DesignAttributeParams::class)),
        tags: ['design','attribute'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Attribute created', content: new JsonContent(ref: ApiAttributeResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    public function create_attribute(Request $request,ElementType $type) {
        $params = new DesignAttributeParams(given_type: $type,namespace: Utilities::getCurrentOrUserNamespace());
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\CreateAttribute(params: $params, is_async: true, tags: ['api-top']);
        $api->createThingTree(tags: ['create-attribute']);

        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }



    /**
     * @throws \Exception
     */
    #[OA\Patch(
        path: '/api/v1/{user_namespace}/design/{element_type}/attribute/{attribute}/edit',
        operationId: 'core.design.edit_attribute',
        description: "Owner admin group can set the following properties to an attribute: ".
        "\nparent, boolean properties, merge methods, access, value policy, value rules, initial value",
        summary: 'Edits the properites of an unpublished attribute  ',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DesignAttributeParams::class)),
        tags: ['design','attribute'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'attribute', description: "The attribute",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchAttribute::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Attribute created', content: new JsonContent(ref: ApiAttributeResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\EditAttribute::class)]
    public function edit_attribute(Request $request,Attribute $attribute) {
        $params = new DesignAttributeParams(given_attribute: $attribute,namespace: Utilities::getCurrentOrUserNamespace());
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\EditAttribute(params: $params, is_async: true, tags: ['api-top']);
        $api->createThingTree(tags: ['edit-attribute']);

        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }







    /**
     * @throws \Exception
     */
    #[OA\Delete(
        path: '/api/v1/{user_namespace}/design/{element_type}/remove_parent',
        operationId: 'core.design.remove_parent',
        description: "The owner or admin group can remove one or more parents from a design using a path. ".
        "\nThis can be removed regardless of the approval status ".
        "\nParents need approval to use, but do not notify the inheritance chain of design changes when dropping that parent",
        summary: 'Removes a parent',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DesignParentParams::class)),
        tags: ['design'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Parents removed', content: new JsonContent(ref: ApiTypeResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\RemoveParent::class)]
    public function remove_parent(Request $request,ElementType $type) {
        $params = new DesignParentParams(given_type: $type);
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\AddParent(params: $params, is_async: true, tags: ['api-top']);
        $api->createThingTree(tags: ['remove-parent']);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[OA\Post(
        path: '/api/v1/{user_namespace}/design/{element_type}/add_parent',
        operationId: 'core.design.add_parent',
        description: "The owner or admin group can add one or more published parents using a path.".
        "\n Regardless who owns it, the event of design_pending can block it from the inheritance chaing".
        "\n If a parent is declared retired or suspended before publishing, then this type cannot be published until that is changed".
        "\nParents need approval to use in the design, and later to publish, to look over any conflicts after the design allowed",
        summary: 'Adds a parent',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DesignParentParams::class)),
        tags: ['design'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) )

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Parents added', content: new JsonContent(ref: ApiTypeResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiEventMarker( Evt\Server\DesignPending::class)]
    #[ApiTypeMarker( Root\Api\Design\AddParent::class)]
    public function add_parent(Request $request,ElementType $type) {
        $params = new DesignParentParams(given_type: $type);
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\RemoveParent(params: $params, is_async: true, tags: ['api-top']);
        $api->createThingTree(tags: ['add-parent']);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     * @throws \Throwable
     */
    #[OA\Post(
        path: '/api/v1/{user_namespace}/design/schedules/create',
        operationId: 'core.design.create_time',
        description: "Makes a schedule that can be used in one or more attributes",
        summary: 'Makes a new schedule',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: Schedule::class)),
        tags: ['design','bounds'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Schedule created', content: new JsonContent(ref: Schedule::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\CreateTime::class)]
    public function create_time(Request $request) {
        $params = Schedule::fromRequest($request);
        $data_out = Root\Api\Design\CreateTime::makeSchedule(params: $params,tags: ['api-top']);
        $http_code = CodeOf::HTTP_ACCEPTED;
        if ($data_out instanceof Thang) { $http_code = CodeOf::HTTP_OK;}
        return  response()->json($data_out,$http_code);
    }


    /**
     * @throws \Exception
     */
    #[OA\Get(
        path: '/api/v1/{user_namespace}/design/schedules/{time_bound}/show',
        operationId: 'core.design.schedules.show',
        description: "Schedules can be changed",
        summary: 'shows a schedule',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: Schedule::class)),
        tags: ['design','bounds'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'time_bound', description: "The schedule",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Schedule edited', content: new JsonContent(ref: Schedule::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Design\EditTime::class)]
    public function show_schedule(TimeBound $bound) {

        $data_out = Root\Api\Design\ShowTime::showSchedule(bound: $bound);
        return  response()->json(['response'=>$data_out],CodeOf::HTTP_OK);
    }


    /**
     * @throws \Exception
     * @throws \Throwable
     */
    #[OA\Patch(
        path: '/api/v1/{user_namespace}/design/schedules/{time_bound}/edit',
        operationId: 'core.design.schedules.edit',
        description: "Schedules can be changed",
        summary: 'Edits a schedule',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: Schedule::class)),
        tags: ['design','bounds'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'time_bound', description: "The schedule",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Schedule edited', content: new JsonContent(ref: Schedule::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\EditTime::class)]
    public function edit_schedule(TimeBound $bound, Request $request) {
        $params = Schedule::validateAndCreate($request->request->all());
        $data_out = Root\Api\Design\EditTime::editSchedule(bound:$bound, params: $params,tags: ['api-top']);
        $http_code = CodeOf::HTTP_ACCEPTED;
        if ($data_out instanceof Thang) { $http_code = CodeOf::HTTP_OK;}
        return  response()->json($data_out,$http_code);
    }


    /**
     * @throws \Exception
     * @throws \Throwable
     */
    #[OA\Delete(
        path: '/api/v1/{user_namespace}/design/schedules/{time_bound}/destroy',
        operationId: 'core.design.schedules.destroy',
        description: "Destroys a time resource, but only if its not used. ",
        summary: 'Remove a time resource  ',
        security: [['bearerAuth' => []]],
        tags: ['design','bounds'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'time_bound', description: "The schedule",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Schedule destroyed', content: new JsonContent(ref: Schedule::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\DestroyTime::class)]
    public function destroy_schedule(TimeBound $bound) {

        $data_out = Root\Api\Design\DestroyTime::destroySchedule(bound:$bound,tags: ['api-top']);
        $http_code = CodeOf::HTTP_ACCEPTED;
        if ($data_out instanceof Thang) { $http_code = CodeOf::HTTP_OK;}
        else if(empty($data_out)) { $data_out = ['uuid'=>$bound->ref_uuid];}

        return  response()->json($data_out,$http_code);
    }


    /**
     * @throws \Exception
     */
    #[OA\Post(
        path: '/api/v1/{user_namespace}/design/location_create',
        operationId: 'core.design.location_create',
        description: "Makes a new geo json 2d or 3d shape",
        summary: 'Makes a new location bound',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DesignLocationParams::class)),
        tags: ['design','bounds'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),
        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Location created', content: new JsonContent(ref: ApiLocationResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\CreateLocation::class)]
    public function location_create(Request $request) {
        $params = new DesignLocationParams();
        $params->fromCollection(new Collection($request->all()));
        $api = new Root\Api\Design\EditLocation(params: $params, is_async: true, tags: ['api-top']);
        $api->createThingTree(tags: ['create-location']);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }

    /**
     * @throws \Exception
     */
    #[OA\Patch(
        path: '/api/v1/{user_namespace}/design/location/{location_bound}/edit',
        operationId: 'core.design.location_edit',
        description: "Change visual properties of a location",
        summary: 'Makes a new location bound',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DesignLocationParams::class)),
        tags: ['design','bounds'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'location_bound', description: "The map or shape",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Location edited', content: new JsonContent(ref: ApiLocationResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\EditLocation::class)]
    public function location_edit(LocationBound $bound,Request $request) {
        $params = new DesignLocationParams(given_bound: $bound);
        $params->fromCollection(new Collection($request->all()));
        $api = new Root\Api\Design\EditLocation(params: $params, is_async: true, tags: ['api-top']);
        $api->createThingTree(tags: ['edit-location']);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[OA\Delete(
        path: '/api/v1/{user_namespace}/design/location/{location_bound}/destroy',
        operationId: 'core.design.destroy_location',
        description: "Destorys a location resource if its not used ",
        summary: 'Destroy a location  ',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DesignLocationParams::class)),
        tags: ['design','bounds'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'location_bound', description: "The map or shape",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Location destroyed', content: new JsonContent(ref: ApiLocationResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\DestroyLocation::class)]
    public function destroy_location(LocationBound $bound,Request $request) {
        $params = new DesignLocationParams(given_bound: $bound);
        $params->fromCollection(new Collection($request->all()));
        $api = new Root\Api\Design\DestroyLocation(params: $params, is_async: true, tags: ['api-top']);
        $api->createThingTree(tags: ['destroy-location']);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[OA\Patch(
        path: '/api/v1/{user_namespace}/design/list_locations',
        operationId: 'core.design.list_locatations',
        description: "Lists locations",
        summary: 'Lists locations  ',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: ListLocationParams::class)),
        tags: ['design','bounds'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),
        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Location results returned', content: new JsonContent(ref: ApiLocationCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Design\ListLocations::class)]
    public function list_locatations(Request $request) {
        $params = new ListLocationParams();
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\ListLocations(params: $params, is_async: false, tags: ['api-top']);
        $api->createThingTree(tags: ['list-locations']);

        $data_out = $api->getDataSnapshot();
        return  response()->json(['response'=>$data_out],$api->getCode());
    }


    /**
     * @throws \Exception
     */
    #[OA\Patch(
        path: '/api/v1/{user_namespace}/design/schedules/list',
        operationId: 'core.design.list_times',
        description: "Lists times",
        summary: 'Lists times  ',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: ScheduleSearchParams::class)),
        tags: ['design','bounds'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),
        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Schedule results returned', content: new JsonContent(ref: ScheduleList::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Design\ListSchedules::class)]
    public function list_times(Request $request) {
        $params = ScheduleSearchParams::fromRequest($request);
        $data_out = Api\Design\ListSchedules::listSchedules(params: $params);
        return  response()->json($data_out,CodeOf::HTTP_OK);
    }


































    #[OA\Post(
        path: '/api/v1/{user_namespace}/design/{element_type}/attribute/{attribute}/create_listener',
        operationId: 'core.design.create_listener',
        description: "Each attribute can have zero or one listeners. If replacing, then earlier must be destroyed. " .
        "\nOwner admin group can create a listener on each attribute, as long as the type is not published ".
        "\nIf inheriting from a parent, and that has a listener, then the listener must be for the same event type ",
        summary: 'Makes a new event listener for an attribute on the design ',
        security: [['bearerAuth' => []]],
        tags: ['design','attribute','rule'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'attribute', description: "The attribute",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchAttribute::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\CreateListener::class)]
    public function create_listener() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Delete(
        path: '/api/v1/{user_namespace}/design/{element_type}/attribute/{attribute}/destroy_listener',
        operationId: 'core.design.destroy_listener',
        description: "Owner admin group can can remove the event listener from the attribute, before publishing ",
        summary: 'Makes a new event listener for an attribute on the design ',
        security: [['bearerAuth' => []]],
        tags: ['design','attribute','rule'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'attribute', description: "The attribute",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchAttribute::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\DestroyListener::class)]
    public function destroy_listener() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/{user_namespace}/design/{element_type}/list_listeners',
        operationId: 'core.design.list_listeners',
        description: "Lists all the listeners, and the attributes that hold them, and the events listened ".
        "\n To see the rules then use the show listener. Any member can see",
        summary: 'Allows testing and debugging of an attribute bounds  ',
        security: [['bearerAuth' => []]],
        tags: ['design','attribute','rule'],
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
    #[ApiTypeMarker( Root\Api\Design\ListListeners::class)]
    public function list_listeners() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/{user_namespace}/design/{element_type}/attribute/{attribute}/show_listener',
        operationId: 'core.design.show_listener',
        description: "Shows the information about the listener, including the rules it uses ".
        "\n This is only shown to the owning namespace members",
        summary: 'Shows information about a specific listener',
        security: [['bearerAuth' => []]],
        tags: ['design','attribute','rule'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'attribute', description: "The attribute",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchAttribute::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Design\ShowListener::class)]
    public function show_listener() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/{user_namespace}/design/{element_type}/attribute/{attribute}/create_rule',
        operationId: 'core.design.create_rule',
        description: "Each listener can have a tree of rules with a single top root, but can have any number of branches or leaves " .
        "\nOwner admin group can add a single rule, or a tree of rules, and that can be attached to the unoccupied root, or to a leaf ",
        summary: 'Creates a rule or rule tree attached to the root or existing rule ',
        security: [['bearerAuth' => []]],
        tags: ['design','attribute','rule'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'attribute', description: "The attribute",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchAttribute::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\CreateListenerRule::class)]
    public function create_rule() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Delete(
        path: '/api/v1/{user_namespace}/design/{element_type}/attribute/{attribute}/rule/{attribute_rule}/destroy_rule',
        operationId: 'core.design.destroy_rule',
        description: "The listener tree can be edited by deleting parts of it. " .
        "\nOwner admin group can prune the tree by branches or leaves ",
        summary: 'Removes a single rule and all its children ',
        security: [['bearerAuth' => []]],
        tags: ['design','attribute','rule'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'attribute', description: "The attribute",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchAttribute::class) ),

            new OA\PathParameter(  name: 'attribute_rule', description: "The rule",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\DestroyListenerRule::class)]
    public function destroy_rule() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/{user_namespace}/design/{element_type}/attribute/{attribute}/rule/{attribute_rule}/edit_rule',
        operationId: 'core.design.edit_rule',
        description: "The listener tree can be edited by changing one rule and its children " .
        "\n For existing rules can change the phase,path,rank,logic, merge method and filter ".
        "\n Owner admin group edit each leaf or branches or the entire tree ",
        summary: 'Edits a single rule and all its children ',
        security: [['bearerAuth' => []]],
        tags: ['design','attribute','rule'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'attribute', description: "The attribute",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchAttribute::class) ),

            new OA\PathParameter(  name: 'attribute_rule', description: "The rule",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\EditListenerRule::class)]
    public function edit_rule() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[OA\Post(
        path: '/api/v1/{user_namespace}/design/{element_type}/attribute/{attribute}/rule/{attribute_rule}/test_rule',
        operationId: 'core.design.test_rule',
        description: "Can test part of the listener tree to figure out bugs ",
        summary: 'Test a rule (or a rule tree) ',
        security: [['bearerAuth' => []]],
        tags: ['design','rule'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'attribute', description: "The attribute",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchAttribute::class) ),

            new OA\PathParameter(  name: 'attribute_rule', description: "The rule",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\EditListenerRule::class)]
    public function test_rule() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Get(
        path: '/api/v1/{user_namespace}/design/{element_type}/attribute/{attribute}/test_listener',
        operationId: 'core.design.test_listener',
        description: "A listener can be tested against a simulated event " .
        "\n The tester provides the set, element, event, as long as the namespace using this can see the set and/or element, can test ",
        summary: 'Test a rule tree against  ',
        security: [['bearerAuth' => []]],
        tags: ['design','attribute','rule'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'attribute', description: "The attribute",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchAttribute::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Design\TestListener::class)]
    public function test_listener() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Get(
        path: '/api/v1/{user_namespace}/design/{element_type}/live_rules/list',
        operationId: 'core.design.list_live_rules',
        description: "Lists the live rules defined for this type. ".
        "\n If the type has public access, then any namespace can see this. Otherwise its members of the owning namespace ",
        summary: 'Lists live rules for the type  ',
        security: [['bearerAuth' => []]],
        tags: ['design','live'],
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
    #[ApiTypeMarker( Root\Api\Design\ListLiveRules::class)]
    public function list_live_rules() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{user_namespace}/design/{element_type}/live_rules/add',
        operationId: 'core.design.add_live_rule',
        description: "Owner admin group can add live rules to be applied after the publishing and making sets out of this " ,
        summary: 'Adds a new live rule to the type before its published ',
        security: [['bearerAuth' => []]],
        tags: ['design','live'],
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
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\AddLiveRule::class)]
    public function add_live_rule() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Delete(
        path: '/api/v1/{user_namespace}/design/{element_type}/live_rules/{live_rule}/remove',
        operationId: 'core.design.remove_live_rule',
        description: "Owner admin group can remove a live rule before its published " ,
        summary: 'Removes a live rule ',
        security: [['bearerAuth' => []]],
        tags: ['design','live'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_type', description: "The type",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'live_rule', description: "The live rule",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\RemoveLiveRule::class)]
    public function remove_live_rule() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


}
