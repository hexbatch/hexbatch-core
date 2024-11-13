<?php

namespace App\Http\Controllers\API;

use App\Helpers\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;
use App\Models\Server;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as CodeOf;

use App\Sys\Res\Types\Stk\Root;


class ElsewhereController extends Controller {


    #[ApiTypeMarker( Root\Api\Elsewhere\Register::class)]
    public function register_elsewhere() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Elsewhere\AskCredentials::class)]
    public function ask_credentials() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Elsewhere\GiveCredentials::class)]
    public function give_credentials() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Elsewhere\ChangeStatus::class)]
    public function change_status() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[ApiTypeMarker( Root\Api\Elsewhere\GiveNamespace::class)]
    public function give_namespace() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Elsewhere\GiveSet::class)]
    public function give_set() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Elsewhere\GiveType::class)]
    public function give_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Elsewhere\GiveElement::class)]
    public function give_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Elsewhere\Purge::class)]
    public function purge_elsewhere() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Elsewhere\ListElsewhere::class)]
    public function list_servers() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[ApiTypeMarker( Root\Api\Elsewhere\Show::class)]
    public function show_server(Server $server): JsonResponse {
        return response()->json([], CodeOf::HTTP_SERVICE_UNAVAILABLE);
    }





}
