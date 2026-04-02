<?php

namespace App\Http\Controllers\Api;

use App\Annotations\Access\TypeOfAccessMarker;
use App\Annotations\ApiAccessMarker;
use App\Annotations\ApiEventMarker;
use App\Annotations\ApiTypeMarker;
use App\Data\ApiParams\Data\Server\ServerInformation;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Sys\Res\Types\Stk\Root;
use App\Sys\Res\Types\Stk\Root\Evt;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class ServerController extends Controller {

    #[OA\Get(
        path: '/api/v1/servers/us',
        operationId: 'core.servers.us',
        description: "Lists public information about the server: all the attributes in the About subtype will be shown, any meta attributes, and name|domain|url ",
        summary: 'Show this server information',
        tags: ['server','public'],
        responses: [
            new OA\Response( response: 200, description: 'The server',content: new JsonContent(ref: ServerInformation::class)),
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::IS_PUBLIC)]
    #[ApiTypeMarker( Root\Api\Server\Show::class)]
    public function us() {
        $server = Server::getDefaultServer();
        $server->server_type->loadMissing(['type_attributes','type_attributes.type_owner','type_schedule','type_parents','type_exposed_attributes','type_exposed_attributes.type_owner']);
        $server_info = ServerInformation::from($server);
        $server_info->server_version = Utilities::getVersionAsString();
        $server_info->server_version_time = Utilities::getVersionDateAsCarbon();
        $server_info->server_install_time = Utilities::getInstallTimeAsCarbon();
        return response()->json($server_info, CodeOf::HTTP_OK);
    }




    #[OA\Get(
        path: '/api/v1/server/admin',
        operationId: 'core.server.admin',
        description: "Lists private information about the server and links to edit each meta and about ",
        summary: 'Show this server private information',
        security: [['bearerAuth' => []]],
        tags: ['server'],
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
        security: [['bearerAuth' => []]],
        tags: ['server'],
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
