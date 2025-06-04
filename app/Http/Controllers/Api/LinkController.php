<?php

namespace App\Http\Controllers\Api;

use App\Annotations\Access\TypeOfAccessMarker;
use App\Annotations\ApiAccessMarker;
use App\Annotations\ApiEventMarker;
use App\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;
use App\Sys\Res\Types\Stk\Root;
use App\Sys\Res\Types\Stk\Root\Evt;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class LinkController extends Controller {



    #[OA\Delete(
        path: '/api/v1/{namespace}/links/unlink',
        operationId: 'core.links.unlink',
        description: "Link admin can remove one or more links they control",
        summary: 'Destroys one or more links',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\LinkDestroyed::class)]
    #[ApiEventMarker( Evt\Server\LinkDestroying::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::LINK_OWNER)]
    #[ApiTypeMarker( Root\Api\Element\UnLink::class)]
    public function unlink_link() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Get(
        path: '/api/v1/{namespace}/links/list',
        operationId: 'core.links.list',
        description: "Link members can see all the links owned by namespaces they belong",
        summary: 'Shows a list of links',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::LINK_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\ListLinks::class)]
    public function list_links() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/{namespace}/links/show',
        operationId: 'core.links.show',
        description: "Link member can see information about a particular link",
        summary: 'Show a link',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::LINK_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\ShowLink::class)]
    public function show_link() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





}
