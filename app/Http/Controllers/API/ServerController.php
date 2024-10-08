<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Server;
use Illuminate\Http\JsonResponse;

//todo implement these
class ServerController extends Controller {
    public function me(): JsonResponse {
        //todo make sure version always included, the user part should be included too
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_SERVICE_UNAVAILABLE);
    }

    public function list_servers(): JsonResponse {
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_SERVICE_UNAVAILABLE);
    }

    public function get_server(Server $server): JsonResponse {
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_SERVICE_UNAVAILABLE);
    }


}
