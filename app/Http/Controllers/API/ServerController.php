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

class ServerController extends Controller {

    #[OA\Get(
        path: '/api/v1/server/us',
        operationId: 'core.server.us',
        description: "Lists public information about the server: all the attributes in the About subtype will be shown, any meta attributes, and name|domain|url ",
        summary: 'Show this server information',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::IS_PUBLIC)]
    #[ApiTypeMarker( Root\Api\Server\Show::class)]
    public function us() {

        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/server/admin',
        operationId: 'core.server.admin',
        description: "Lists private information about the server and links to edit each meta and about ",
        summary: 'Show this server private information',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Server\ShowAdmin::class)]
    public function show_admin() {

        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Patch(
        path: '/api/v1/server/edit',
        operationId: 'core.server.edit',
        description: "Edits only the name,domain and url. The about and the meta have to be done independ, but the admin view has the links to those",
        summary: 'Edit this server information',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\ServerEdited::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Server\Edit::class)]
    public function edit_server() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

}
