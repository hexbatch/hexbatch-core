<?php

namespace App\Http\Controllers\Api;

use App\Annotations\Access\TypeOfAccessMarker;
use App\Annotations\ApiAccessMarker;
use App\Annotations\ApiTypeMarker;
use App\Data\ApiParams\OpenApi\Common\Resources\HexbatchNamespace;
use App\Data\ApiParams\OpenApi\Common\Resources\HexbatchResource;
use App\Http\Controllers\Controller;
use App\Sys\Res\Types\Stk\Root;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as CodeOf;


class PhaseController extends Controller {



    #[OA\Get(
        path: '/api/v1/{user_namespace}/phases/{phase}/show',
        operationId: 'core.phases.show',
        description: "Shows a phase if the caller is a member of the type which manages the phase. Also shows system phases. Shows stats about the phase",
        summary: 'Show details about a phase',
        security: [['bearerAuth' => []]],
        tags: ['phase'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'phase', description: "The phase",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    #[ApiTypeMarker( Root\Api\Phase\Show::class)]
    public function show_phase() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/{user_namespace}/phases/list',
        operationId: 'core.phases.list',
        description: "Phases are listed if the caller is a member of the type which manages the phase, filterable via the type handle. System phases are listed here too",
        summary: 'List phases',
        security: [['bearerAuth' => []]],
        tags: ['phase'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    #[ApiTypeMarker( Root\Api\Phase\ListPhases::class)]
    public function list_phases() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


}
