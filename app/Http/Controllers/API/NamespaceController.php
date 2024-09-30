<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
//todo implement these
class NamespaceController extends Controller {
    public function create_namespace(Request $request): JsonResponse {
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_SERVICE_UNAVAILABLE);
    }

    public function list_namespaces(?User $user = null): JsonResponse {
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_SERVICE_UNAVAILABLE);
    }

    public function get_namespace(UserType $userType): JsonResponse {
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_SERVICE_UNAVAILABLE);
    }

    public function destroy_namespace(UserType $userType): JsonResponse {
        return response()->json([], \Symfony\Component\HttpFoundation\Response::HTTP_SERVICE_UNAVAILABLE);
    }
}
