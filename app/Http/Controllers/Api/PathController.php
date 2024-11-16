<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Annotations\Access\TypeOfAccessMarker;
use App\Helpers\Annotations\ApiAccessMarker;
use App\Helpers\Annotations\ApiEventMarker;
use App\Helpers\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;


use App\Sys\Res\Types\Stk\Root;
use Symfony\Component\HttpFoundation\Response as CodeOf;
use OpenApi\Attributes as OA;
use App\Sys\Res\Types\Stk\Root\Evt;

class PathController extends Controller {


    #[OA\Patch(
        path: '/api/v1/{namespace}/paths/publish',
        operationId: 'core.paths.publish',
        description: "Paths can be used in other api calls after publishing",
        summary: 'Publish a path, marking it as workable',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::PATH_ADMIN)]
    #[ApiTypeMarker( Root\Api\Path\Publish::class)]
    public function publish_path() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[OA\Patch(
        path: '/api/v1/{namespace}/paths/add_handle',
        operationId: 'core.paths.add_handle',
        description: "Paths can be grouped, organized and controlled together",
        summary: 'Add element handle to a path',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\PathHandleAdded::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::PATH_ADMIN)]
    #[ApiTypeMarker( Root\Api\Path\AddHandle::class)]
    public function add_handle() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Patch(
        path: '/api/v1/{namespace}/paths/remove_handle',
        operationId: 'core.paths.remove_handle',
        description: "Handles can be removed at any time, and left empty or new ones added",
        summary: 'Remove element handle from a path',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\PathHandleRemoved::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::PATH_ADMIN)]
    #[ApiTypeMarker( Root\Api\Path\RemoveHandle::class)]
    public function remove_handle() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Get(
        path: '/api/v1/{namespace}/paths/list',
        operationId: 'core.paths.list',
        description: "Can filter by handle or see all paths",
        summary: 'Lists the paths this namespace is a member of',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::PATH_MEMBER)]
    #[ApiTypeMarker( Root\Api\Path\ListAll::class)]
    public function list_paths() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/{namespace}/paths/show',
        operationId: 'core.paths.show',
        description: "Members can see times and what uses a path",
        summary: 'Shows a path',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::PATH_MEMBER)]
    #[ApiTypeMarker( Root\Api\Path\Show::class)]
    public function show_path() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/paths/copy',
        operationId: 'core.paths.copy',
        description: "Paths can be complicated to make and test, copying them for other use and editing those can be helpful. The new path is unpublished",
        summary: 'Copies a path, with the caller the owner of the new path',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::PATH_MEMBER)]
    #[ApiTypeMarker( Root\Api\Path\Copy::class)]
    public function copy_path() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/paths/create',
        operationId: 'core.paths.create',
        description: "The caller is the path owner. The name and phase is set",
        summary: 'Creates a new empty path that is unpublished',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::PATH_OWNER)]
    #[ApiTypeMarker( Root\Api\Path\Create::class)]
    public function create_path() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Patch(
        path: '/api/v1/{namespace}/paths/edit',
        operationId: 'core.paths.edit',
        description: "Handles can be removed at any time, and left empty or new ones added",
        summary: 'Change the path name and phase',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\PathHandleAdded::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::PATH_ADMIN)]
    #[ApiTypeMarker( Root\Api\Path\Edit::class)]
    public function edit_path() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Post(
        path: '/api/v1/{namespace}/paths/destroy',
        operationId: 'core.paths.destry',
        description: "No events when destroying, but if used for something will generate an error",
        summary: 'Destroys a path',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::PATH_OWNER)]
    #[ApiTypeMarker( Root\Api\Path\Destroy::class)]
    public function destroy_path() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Post(
        path: '/api/v1/{namespace}/paths/create_part',
        operationId: 'core.paths.create_part',
        description: "Add a single part, or a tree of parts, and that can be attached to the unoccupied root, or to a leaf",
        summary: 'Creates a part or part tree attached to the root or existing rule',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::PATH_ADMIN)]
    #[ApiTypeMarker( Root\Api\Path\CreatePart::class)]
    public function create_part() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Delete(
        path: '/api/v1/{namespace}/paths/destroy_part',
        operationId: 'core.paths.destroy_part',
        description: "The part tree can be trimmed by deleting one rule and its children. This can also delete the root (and all the parts)",
        summary: 'Removed a single part and all its children',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::PATH_ADMIN)]
    #[ApiTypeMarker( Root\Api\Path\DestroyPart::class)]
    public function destroy_part() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Patch(
        path: '/api/v1/{namespace}/paths/edit_part',
        operationId: 'core.paths.edit_part',
        description: "The part tree can be edited by changing one rule and its children. This can also edit the root (and all the parts)",
        summary: 'Edits a single part and all its children',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::PATH_ADMIN)]
    #[ApiTypeMarker( Root\Api\Path\EditPart::class)]
    public function edit_part() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Get(
        path: '/api/v1/{namespace}/paths/show_part',
        operationId: 'core.paths.show_part',
        description: "Show a part by itself or a tree, if it has children.",
        summary: 'Shows a part and its tree',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::PATH_MEMBER)]
    #[ApiTypeMarker( Root\Api\Path\ShowPartTree::class)]
    public function show_part_tree() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/{namespace}/paths/test_part',
        operationId: 'core.paths.test_part',
        description: "Test a part by itself or a tree, if it has children. The tree (or part) will pretend to be the entire search. No events called",
        summary: 'Test a path tree',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::PATH_MEMBER)]
    #[ApiTypeMarker( Root\Api\Path\TestPart::class)]
    public function test_part() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/{namespace}/paths/search',
        operationId: 'core.paths.search',
        description: "If handle is set, event will be called. Handle can filter, augment or refine",
        summary: 'Search with a path',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Element\SearchResults::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::PATH_MEMBER)]
    #[ApiTypeMarker( Root\Api\Path\Search::class)]
    public function search() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/{namespace}/paths/test',
        operationId: 'core.paths.test',
        description: "Used before a path is published, can test to see how the parts are working out, no events called",
        summary: 'Test a search',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::PATH_MEMBER)]
    #[ApiTypeMarker( Root\Api\Path\Test::class)]
    public function test() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


}
