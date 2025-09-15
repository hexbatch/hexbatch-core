<?php

namespace App\Http\Controllers\Api;

use App\Annotations\Access\TypeOfAccessMarker;
use App\Annotations\ApiAccessMarker;
use App\Annotations\ApiEventMarker;
use App\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;
use App\Models\ElementLink;
use App\OpenApi\ApiResults\Set\ApiLinkerCollectionResponse;
use App\OpenApi\ApiResults\Set\ApiLinkerResponse;
use App\OpenApi\Params\Actioning\Element\LinkSelectParams;
use App\OpenApi\Results\Callbacks\HexbatchCallbackCollectionResponse;
use App\Sys\Res\Types\Stk\Root;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Things\OpenApi\Things\ThingResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class LinkController extends Controller {


    /**
     * @throws \Exception
     */
    #[OA\Delete(
        path: '/api/v1/{user_namespace}/links/phase/{working_phase}/link/{element_link}/unlink',
        operationId: 'core.links.unlink',
        description: "Link admin can remove a links they control",
        summary: 'Destroys a link',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: LinkSelectParams::class)),
        tags: ['link'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase used",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element_link', description: "The link",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Unlinked', content: new JsonContent(ref: ApiLinkerResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiEventMarker( Evt\Server\LinkDestroyed::class)]
    #[ApiEventMarker( Evt\Server\LinkDestroying::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::LINK_OWNER)]
    #[ApiTypeMarker( Root\Api\Element\UnLink::class)]
    public function unlink_link(Request $request,ElementLink $link) {
        $params = new LinkSelectParams(given_link: $link);
        $params->fromCollection(new Collection($request->all()));
        $api = new Root\Api\Element\UnLink(params: $params, is_async: true, tags: ['api-top']);
        $api->createThingTree(tags: ['unlink']);

        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }



    #[OA\Get(
        path: '/api/v1/{user_namespace}/links/phase/{working_phase}/list',
        operationId: 'core.links.list',
        description: "Link members can see all the links owned by namespaces they belong",
        summary: 'Shows a list of links',
        security: [['bearerAuth' => []]],
        tags: ['link'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase used",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Type info listeed', content: new JsonContent(ref: ApiLinkerCollectionResponse::class)),
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::LINK_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\ListLinks::class)]
    public function list_links() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/{user_namespace}/links/phase/{working_phase}/link/{element_link}/show',
        operationId: 'core.links.show',
        description: "Link member can see information about a particular link",
        summary: 'Show a link',
        security: [['bearerAuth' => []]],
        tags: ['link'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase used",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element_link', description: "The link",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Found link', content: new JsonContent(ref: ApiLinkerResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_FORBIDDEN, description: 'Not a member of the namespace',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::LINK_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\ShowLink::class)]
    public function show_link(ElementLink $link) {
        return  response()->json(new ApiLinkerResponse(linker: $link),CodeOf::HTTP_OK);
    }





}
