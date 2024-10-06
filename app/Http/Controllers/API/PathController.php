<?php

namespace App\Http\Controllers\API;

use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
;

use App\Http\Resources\PathResource;
use App\Models\Path;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PathController extends Controller {
    public function create_path(Request $request): JsonResponse {
        $path = Path::collectPath(collect: $request->collect(),owner: Utilities::getCurrentNamespace());
        $out = Path::buildPath(id: $path->id);
        return response()->json(new PathResource($out,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function edit_path(Request $request,Path $path): JsonResponse {
        $pathy = Path::collectPath(collect: $request->collect(),owner: Utilities::getCurrentNamespace(),path: $path);
        $out = Path::buildPath(id: $pathy->id);
        return response()->json(new PathResource($out,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }
}
