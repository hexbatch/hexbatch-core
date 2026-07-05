<?php

namespace App\Http\Controllers\Api;

use App\Annotations\Access\TypeOfAccessMarker;
use App\Annotations\ApiAccessMarker;
use App\Annotations\ApiTypeMarker;
use App\Data\ApiParams\Data\Elements\Responses\LinkList;
use App\Data\ApiParams\OpenApi\Common\Resources\HexbatchNamespace;
use App\Data\ApiParams\OpenApi\Common\Resources\HexbatchResource;
use App\Http\Controllers\Controller;
use App\Models\ElementLink;
use App\Sys\Res\Types\Stk\Root;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class LinkController extends Controller {






    #[OA\Get(
        path: '/api/v1/{user_namespace}/links/phase/{working_phase}/list',
        operationId: 'core.links.list',
        description: "Link members can see all the links owned by namespaces they belong",
        summary: 'Shows a list of links',
        security: [['bearerAuth' => []]],
        tags: ['link'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase used",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Type info listeed', content: new JsonContent(ref: LinkList::class)),
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
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase used",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'element_link', description: "The link",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::LINK_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\ShowLink::class)]
    public function show_link(ElementLink $link) {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





}
