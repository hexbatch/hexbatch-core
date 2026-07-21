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



    #[OA\Post(
        path: '/api/v1/{user_namespace}/phases/tree/copy',
        operationId: 'core.phases.tree.copy',
        description: "",
        summary: 'Copy nested sets from one phase to another',
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
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Phase\CopyTree::class)]
    public function copy_tree() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[OA\Post(
        path: '/api/v1/{user_namespace}/phases/tree/move',
        operationId: 'core.phases.tree.move',
        description: "",
        summary: 'Move nested sets from one phase to another',
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
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Phase\MoveTree::class)]
    public function move_tree() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }






    #[OA\Delete(
        path: '/api/v1/{user_namespace}/phases/tree/delete',
        operationId: 'core.phases.tree.delete.create',
        description: "",
        summary: "Remove nested sets from a phase",
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
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Phase\DeleteTree::class)]
    public function delete_tree() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{user_namespace}/phases/create',
        operationId: 'core.phases.create',
        description: "",
        summary: 'Create a new phase',
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
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Phase\Create::class)]
    public function create_phase() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Delete(
        path: '/api/v1/{user_namespace}/phases/tree/purge',
        operationId: 'core.phases.purge',
        description: "",
        summary: 'Deletes a phase',
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
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Phase\Purge::class)]
    public function purge_phase() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




}
