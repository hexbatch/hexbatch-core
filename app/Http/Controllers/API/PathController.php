<?php

namespace App\Http\Controllers\API;

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

class PathController extends Controller {
    public function create_path(Request $request,UserNamespace $namespace): JsonResponse {
        $out = Path::collectPath(collect: $request->collect(),owner: $namespace);
        return response()->json(new PathResource($out,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    /**
     * @throws \Exception
     */
    public function update_path(Request $request, UserNamespace $namespace,Path $path): JsonResponse {
        Utilities::ignoreVar($namespace); //checked in route
        $path->path_root_part->delete_subtree();
        $out = Path::collectPath(collect: $request->collect(),path: $path);
        return response()->json(new PathResource($out,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }


    public function list_paths(UserNamespace $namespace): JsonResponse {
        $out = Path::buildPath(owner_namespace_id: $namespace->id)->cursorPaginate();
        return response()->json(new PathCollection($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }





    public function add_part_subtree(Request $request, UserNamespace $namespace,Path $path, PathPart $path_part): JsonResponse {

        PathPart::collectPathPart(collect: $request->collect(),owner: $path,parent: $path_part);
        $out = Path::buildPath(id: $path->id,owner_namespace_id: $namespace->id)->first();
        return response()->json(new PathResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }


    /**
     * @throws \Exception
     */
    public function edit_part(Request $request, UserNamespace $namespace,Path $path, PathPart $path_part): JsonResponse {
        PathPart::collectPathPart(collect: $request->collect(),owner: $path,part: $path_part);
        $out = Path::buildPath(id: $path->id,owner_namespace_id: $namespace->id)->first();
        return response()->json(new PathResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }


    /**
     * @throws \Exception
     */
    public function delete_path( UserNamespace $namespace,Path $path): JsonResponse {
        $path->path_root_part->delete_subtree();
        $out = Path::buildPath(id: $path->id,owner_namespace_id: $namespace->id)->first();
        return response()->json(new PathResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    /**
     * @throws \Exception
     */
    public function delete_part_subtree(UserNamespace $namespace,Path $path, PathPart $path_part): JsonResponse {
        $path_part->delete_subtree();
        $out = Path::buildPath(id: $path->id,owner_namespace_id: $namespace->id)->first();
        return response()->json(new PathResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }



    public function get_part(UserNamespace $namespace,Path $path, PathPart $path_part): JsonResponse {
        Utilities::ignoreVar($namespace); //checked in route
        $out = PathPart::buildPathPart(id: $path_part->id,owner_path_id: $path->id)->first();
        return response()->json(new PathPartResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function path_test(Request $request, ElementType $element_type, Attribute $attribute): JsonResponse {
        Utilities::ignoreVar($request,$element_type,$attribute); //checked in the middleware
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_NOT_IMPLEMENTED);
    }
}
