<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ElementController extends Controller {
    public function get_user(User $some_user): JsonResponse {
        //the wiring here converts a user uuid or username to the user and then the response includes information about the element of the user
        return response()->json(new UserResource($some_user), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }
}
