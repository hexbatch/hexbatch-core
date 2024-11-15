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

class PhaseController extends Controller {



    #[OA\Get(
        path: '/api/v1/phases/show',
        operationId: 'core.phases.show',
        description: "Shows a phase if the caller is a member of the type which manages the phase. Shows stats about the phase",
        summary: 'List phases',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Phase\Show::class)]
    public function show_phase() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/phases/list',
        operationId: 'core.phases.list',
        description: "Phases are listed if the caller is a member of the type which manages the phase, filterable via the type handle",
        summary: 'List phases',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Phase\ListPhases::class)]
    public function list_phases() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


}
