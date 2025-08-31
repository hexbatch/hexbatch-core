<?php

namespace App\Http\Controllers\Api;

use App\Annotations\Access\TypeOfAccessMarker;
use App\Annotations\ApiAccessMarker;
use App\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;
use App\OpenApi\Common\Resources\HexbatchNamespace;
use App\Sys\Res\Types\Stk\Root;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as CodeOf;


class PhaseController extends Controller {



    #[OA\Get(
        path: '/api/v1/{namespace}/phases/show',
        operationId: 'core.phases.show',
        description: "Shows a phase if the caller is a member of the type which manages the phase. Also shows system phases. Shows stats about the phase",
        summary: 'Show details about a phase',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
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
        path: '/api/v1/{namespace}/phases/list',
        operationId: 'core.phases.list',
        description: "Phases are listed if the caller is a member of the type which manages the phase, filterable via the type handle. System phases are listed here too",
        summary: 'List phases',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
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
