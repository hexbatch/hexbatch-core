<?php

namespace App\Http\Controllers\API;

use App\Helpers\Annotations\ApiTypeMarker;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;

use App\Http\Resources\PathCollection;
use App\Http\Resources\PathPartResource;
use App\Http\Resources\PathResource;
use App\Models\Attribute;
use App\Models\ElementType;
use App\Models\Path;
use App\Models\PathPart;
use App\Models\UserNamespace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Sys\Res\Types\Stk\Root;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class PathController extends Controller {



    #[ApiTypeMarker( Root\Api\Path\Publish::class)]
    public function publish_path() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Path\AddHandle::class)]
    public function add_handle() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Path\RemoveHandle::class)]
    public function remove_handle() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Path\ListAll::class)]
    public function list_paths() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Path\Show::class)]
    public function show_path() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[ApiTypeMarker( Root\Api\Path\Copy::class)]
    public function copy_path() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Path\Create::class)]
    public function create_path() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Path\Edit::class)]
    public function edit_path() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Path\Destroy::class)]
    public function destroy_path() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[ApiTypeMarker( Root\Api\Path\CreatePart::class)]
    public function create_part() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[ApiTypeMarker( Root\Api\Path\DestroyPart::class)]
    public function destroy_part() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Path\EditPart::class)]
    public function edit_part() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Path\ShowPartTree::class)]
    public function show_part_tree() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Path\TestPart::class)]
    public function test_part() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Path\Search::class)]
    public function search() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Path\Test::class)]
    public function test() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    //_.~"~._.~"~._.~"~._.~"~.__.~"~._.~"~._.~"~._.~"~.__.~"~._.~"~._.~"~._.~"~.__.~"~._.~"~._.~"~._.~"~.__.~"~._.~"~._.~"~._.~"~._
























    public function create_path_x(Request $request, UserNamespace $namespace): JsonResponse {
        $out = Path::collectPath(collect: $request->collect(),owner: $namespace);
        return response()->json(new PathResource($out,null,3),CodeOf::HTTP_OK);
    }

    /**
     * @throws \Exception
     */
    public function update_path_x(Request $request, UserNamespace $namespace, Path $path): JsonResponse {
        Utilities::ignoreVar($namespace); //checked in route
        $path->path_root_part->delete_subtree();
        $out = Path::collectPath(collect: $request->collect(),path: $path);
        return response()->json(new PathResource($out,null,3),CodeOf::HTTP_OK);
    }


    public function list_paths_x(UserNamespace $namespace): JsonResponse {
        $out = Path::buildPath(owner_namespace_id: $namespace->id)->cursorPaginate();
        return response()->json(new PathCollection($out),CodeOf::HTTP_OK);
    }





    public function add_part_subtree_x(Request $request, UserNamespace $namespace, Path $path, PathPart $path_part): JsonResponse {

        PathPart::collectPathPart(collect: $request->collect(),owner: $path,parent: $path_part);
        $out = Path::buildPath(id: $path->id,owner_namespace_id: $namespace->id)->first();
        return response()->json(new PathResource($out),CodeOf::HTTP_OK);
    }


    /**
     * @throws \Exception
     */
    public function edit_part_x(Request $request, UserNamespace $namespace, Path $path, PathPart $path_part): JsonResponse {
        PathPart::collectPathPart(collect: $request->collect(),owner: $path,part: $path_part);
        $out = Path::buildPath(id: $path->id,owner_namespace_id: $namespace->id)->first();
        return response()->json(new PathResource($out),CodeOf::HTTP_OK);
    }


    /**
     * @throws \Exception
     */
    public function delete_path_x(UserNamespace $namespace, Path $path): JsonResponse {
        $path->path_root_part->delete_subtree();
        $out = Path::buildPath(id: $path->id,owner_namespace_id: $namespace->id)->first();
        return response()->json(new PathResource($out),CodeOf::HTTP_OK);
    }

    /**
     * @throws \Exception
     */
    public function delete_part_subtree_x(UserNamespace $namespace, Path $path, PathPart $path_part): JsonResponse {
        $path_part->delete_subtree();
        $out = Path::buildPath(id: $path->id,owner_namespace_id: $namespace->id)->first();
        return response()->json(new PathResource($out),CodeOf::HTTP_OK);
    }



    public function get_part_x(UserNamespace $namespace, Path $path, PathPart $path_part): JsonResponse {
        Utilities::ignoreVar($namespace); //checked in route
        $out = PathPart::buildPathPart(id: $path_part->id,owner_path_id: $path->id)->first();
        return response()->json(new PathPartResource($out),CodeOf::HTTP_OK);
    }

    public function path_test_x(Request $request, ElementType $element_type, Attribute $attribute): JsonResponse {
        Utilities::ignoreVar($request,$element_type,$attribute); //checked in the middleware
        return response()->json([],CodeOf::HTTP_NOT_IMPLEMENTED);
    }
}
